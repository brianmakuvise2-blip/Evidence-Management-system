<?php

namespace App\Services;

/**
 * Zero-dependency PDF table builder.
 * Generates valid PDF 1.4 binaries with multi-page support.
 */
class PdfService
{
    private const MARGIN  = 36;
    private const LINE_H  = 14;
    private const ROW_PAD = 3;

    private int    $pageW;
    private int    $pageH;
    private string $buf  = '';
    private array  $xref = [];

    /**
     * Build a table PDF.
     *
     * @param string $title
     * @param string $subtitle   e.g. "Generated 2026-05-05 10:30"
     * @param array  $columns    [['label' => 'Name', 'width' => 120], ...]  (widths are proportional)
     * @param array  $rows       [['val1', 'val2', ...], ...]
     * @param bool   $landscape
     * @return string  PDF binary
     */
    public function build(
        string $title,
        string $subtitle,
        array  $columns,
        array  $rows,
        bool   $landscape = false
    ): string {
        $this->pageW = $landscape ? 842 : 595;
        $this->pageH = $landscape ? 595 : 842;
        $this->buf   = '';
        $this->xref  = [];

        // Scale column widths to fill the content area
        $contentW   = $this->pageW - self::MARGIN * 2;
        $totalGiven = array_sum(array_column($columns, 'width'));
        $scale      = $totalGiven > 0 ? $contentW / $totalGiven : 1;
        $widths     = array_map(fn($c) => (int) round($c['width'] * $scale), $columns);

        // Y positions (PDF origin = bottom-left)
        $titleY      = $this->pageH - self::MARGIN - 4;
        $subY        = $titleY - 18;
        $headerY     = $subY - 20;   // top of the first table row (header)
        $bottomY     = self::MARGIN + 24;
        $rowsPerPage = max(1, (int) floor(($headerY - self::LINE_H - $bottomY) / self::LINE_H));

        $chunks   = !empty($rows) ? array_chunk($rows, $rowsPerPage) : [[]];
        $totalPgs = count($chunks);

        // Pre-build all content streams (we need their byte lengths)
        $streams = [];
        foreach ($chunks as $i => $pageRows) {
            $streams[] = $this->pageContent(
                $title, $subtitle, $columns, $widths, $pageRows,
                $i + 1, $totalPgs, $titleY, $subY, $headerY, $contentW
            );
        }

        // Object layout:
        //  1 = Catalog
        //  2 = Pages
        //  3 = Font /Helvetica (body)
        //  4 = Font /Helvetica-Bold (headings)
        //  5,6  = Page1, Stream1
        //  7,8  = Page2, Stream2  ...
        $numFixed   = 4;
        $pageIds    = [];
        $streamIds  = [];
        for ($i = 0; $i < $totalPgs; $i++) {
            $pageIds[]   = $numFixed + 1 + $i * 2;
            $streamIds[] = $numFixed + 2 + $i * 2;
        }
        $totalObjs = $numFixed + $totalPgs * 2;

        // ---- write PDF header + objects ----
        $this->buf = "%PDF-1.4\n%\xe2\xe3\xcf\xd3\n";

        $kids = implode(' ', array_map(fn($id) => "$id 0 R", $pageIds));
        $this->obj(1, "<< /Type /Catalog /Pages 2 0 R >>");
        $this->obj(2, "<< /Type /Pages /Kids [$kids] /Count $totalPgs >>");
        $this->obj(3, "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>");
        $this->obj(4, "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>");

        for ($i = 0; $i < $totalPgs; $i++) {
            $len = strlen($streams[$i]);
            $this->obj(
                $pageIds[$i],
                "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 {$this->pageW} {$this->pageH}]" .
                " /Resources << /Font << /F1 3 0 R /F2 4 0 R >> >>" .
                " /Contents {$streamIds[$i]} 0 R >>"
            );
            $this->obj(
                $streamIds[$i],
                "<< /Length $len >>\nstream\n{$streams[$i]}\nendstream"
            );
        }

        // ---- xref + trailer ----
        $xrefOff = strlen($this->buf);
        $xref    = "xref\n0 " . ($totalObjs + 1) . "\n";
        $xref   .= "0000000000 65535 f \n";
        for ($i = 1; $i <= $totalObjs; $i++) {
            $xref .= sprintf("%010d 00000 n \n", $this->xref[$i]);
        }
        $xref .= "trailer\n<< /Size " . ($totalObjs + 1) . " /Root 1 0 R >>\n";
        $xref .= "startxref\n$xrefOff\n%%EOF\n";

        return $this->buf . $xref;
    }

    // -----------------------------------------------------------------------

    private function obj(int $id, string $def): void
    {
        $this->xref[$id] = strlen($this->buf);
        $this->buf .= "$id 0 obj\n$def\nendobj\n";
    }

    private function pageContent(
        string $title, string $subtitle,
        array  $columns, array $widths,
        array  $rows, int $pageNum, int $totalPgs,
        int $titleY, int $subY, int $headerY, int $contentW
    ): string {
        $s      = '';
        $lx     = self::MARGIN;
        $totalW = array_sum($widths);

        // ---- title ----
        $s .= $this->text($lx, $titleY, $this->e($title), 'F2', 14, '0 0 0');

        // ---- subtitle ----
        $s .= $this->text($lx, $subY, $this->e($subtitle), 'F1', 8, '0.4 0.4 0.4');

        // ---- separator line ----
        $sepY = $subY - 8;
        $s .= "0.1 0.1 0.18 RG 0.5 w $lx $sepY m " . ($lx + $totalW) . " $sepY l S\n";

        // ---- column header background ----
        $hBgY = $headerY - self::LINE_H;
        $s .= "0.1 0.1 0.18 rg\n{$lx} {$hBgY} {$totalW} " . self::LINE_H . " re f\n";

        // ---- column header text (white, bold) ----
        $s .= "BT /F2 8 Tf 1 1 1 rg\n";
        $x = $lx;
        foreach ($columns as $i => $col) {
            $px = $x + self::ROW_PAD;
            $py = $hBgY + self::ROW_PAD + 1;
            $s .= "1 0 0 1 $px $py Tm (" . $this->e($col['label']) . ") Tj\n";
            $x += $widths[$i];
        }
        $s .= "ET\n";

        // ---- data rows ----
        $curY = $hBgY - self::LINE_H;
        foreach ($rows as $ri => $row) {
            if ($ri % 2 === 1) {
                $s .= "0.95 0.96 1.0 rg\n{$lx} {$curY} {$totalW} " . self::LINE_H . " re f\n";
            }

            $s .= "BT /F1 8 Tf 0 0 0.1 rg\n";
            $x = $lx;
            foreach ($row as $ci => $cell) {
                $text = $this->truncate((string) $cell, $widths[$ci] ?? 50);
                $px   = $x + self::ROW_PAD;
                $py   = $curY + self::ROW_PAD + 1;
                $s   .= "1 0 0 1 $px $py Tm (" . $this->e($text) . ") Tj\n";
                $x   += $widths[$ci] ?? 0;
            }
            $s .= "ET\n";

            // row divider
            $rb = $curY;
            $s .= "0.88 0.88 0.92 RG 0.3 w {$lx} {$rb} m " . ($lx + $totalW) . " {$rb} l S\n";

            $curY -= self::LINE_H;
        }

        // ---- footer ----
        $footerText = "Evidence Management System  |  Page $pageNum of $totalPgs  |  " . date('d F Y, H:i');
        $footY      = self::MARGIN - 18;
        $s .= "0.7 0.7 0.7 RG 0.5 w {$lx} {$footY} m " . ($lx + $totalW) . " {$footY} l S\n";
        $s .= $this->text($lx, $footY - 10, $this->e($footerText), 'F1', 7, '0.5 0.5 0.5');

        return $s;
    }

    private function text(int $x, int $y, string $escaped, string $font, int $size, string $rgb): string
    {
        return "BT /$font $size Tf $rgb rg 1 0 0 1 $x $y Tm ($escaped) Tj ET\n";
    }

    private function e(string $text): string
    {
        $text = preg_replace('/[^\x20-\x7E]/', ' ', $text);
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }

    private function truncate(string $text, int $colWidth): string
    {
        $maxChars = max(4, (int) floor($colWidth / 5.0));
        if (mb_strlen($text) <= $maxChars) {
            return $text;
        }
        return mb_substr($text, 0, $maxChars - 2) . '..';
    }
}
