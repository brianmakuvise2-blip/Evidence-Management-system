@extends('layouts.admin')

@section('title', 'Preview Evidence')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-3">
        <div class="col-md-8">
            <h3 class="mb-0"><i class="bi bi-eye"></i> Preview Evidence</h3>
            <p class="text-muted mb-0">{{ $evidence->title }}</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('evidence.show', $evidence) }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Evidence
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3 gap-2 flex-wrap">
                <div>
                    <button id="prevPage" class="btn btn-outline-primary btn-sm me-2" disabled>
                        <i class="bi bi-arrow-left"></i> Previous
                    </button>
                    <button id="nextPage" class="btn btn-outline-primary btn-sm" disabled>
                        Next <i class="bi bi-arrow-right"></i>
                    </button>
                </div>
                <div>
                    <span class="me-3">Page <strong id="pageNum">0</strong> of <strong id="pageCount">0</strong></span>
                    <span class="text-muted">Zoom:</span>
                    <button id="zoomOut" class="btn btn-outline-secondary btn-sm">-</button>
                    <button id="zoomIn" class="btn btn-outline-secondary btn-sm">+</button>
                </div>
            </div>
            <div class="border rounded overflow-hidden" style="background: #f8f9fa;">
                <canvas id="pdfCanvas" class="w-100" style="display: block; background: #ffffff; max-width: 100%; height: auto;"></canvas>
            </div>
            <div id="previewStatus" class="alert alert-info mt-3 mb-0">
                Loading PDF preview...
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const previewUrl = "{{ route('evidence.file', $evidence) }}";
        const canvas = document.getElementById('pdfCanvas');
        const context = canvas.getContext('2d');
        const pageNumElement = document.getElementById('pageNum');
        const pageCountElement = document.getElementById('pageCount');
        const prevPageButton = document.getElementById('prevPage');
        const nextPageButton = document.getElementById('nextPage');
        const zoomInButton = document.getElementById('zoomIn');
        const zoomOutButton = document.getElementById('zoomOut');
        const previewStatus = document.getElementById('previewStatus');

        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';

        let pdfDoc = null;
        let currentPage = 1;
        let scale = 1.2;

        function displayPreviewError(message) {
            canvas.style.display = 'none';
            previewStatus.className = 'alert alert-danger mt-3 mb-0';
            previewStatus.textContent = message;
            console.error(message);
        }

        function renderPage(pageNumber) {
            pdfDoc.getPage(pageNumber).then(function(page) {
                const viewport = page.getViewport({ scale: scale });
                canvas.height = viewport.height;
                canvas.width = viewport.width;
                canvas.style.maxWidth = '100%';
                canvas.style.height = 'auto';
                canvas.style.backgroundColor = '#ffffff';

                const renderContext = {
                    canvasContext: context,
                    viewport: viewport
                };

                const renderTask = page.render(renderContext);
                renderTask.promise.then(function() {
                    pageNumElement.textContent = pageNumber;
                    pageCountElement.textContent = pdfDoc.numPages;
                    previewStatus.textContent = 'PDF preview loaded successfully.';
                    previewStatus.className = 'alert alert-success mt-3 mb-0';
                    prevPageButton.disabled = pageNumber <= 1;
                    nextPageButton.disabled = pageNumber >= pdfDoc.numPages;
                }).catch(function(error) {
                    console.error('PDF render failed:', error);
                    displayPreviewError('Unable to render PDF preview.');
                });
            }).catch(function(error) {
                console.error('Failed to get PDF page:', error);
                displayPreviewError('Unable to load PDF page.');
            });
        }

        function loadPdf() {
            pdfjsLib.getDocument({ url: previewUrl, withCredentials: true }).promise
            .then(pdf => {
                pdfDoc = pdf;
                renderPage(currentPage);
            })
            .catch(error => {
                console.error('PDF load failed:', error);
                displayPreviewError('Unable to load PDF preview.');
            });
        }

        prevPageButton.addEventListener('click', function() {
            if (currentPage <= 1) return;
            currentPage -= 1;
            renderPage(currentPage);
        });

        nextPageButton.addEventListener('click', function() {
            if (!pdfDoc || currentPage >= pdfDoc.numPages) return;
            currentPage += 1;
            renderPage(currentPage);
        });

        zoomInButton.addEventListener('click', function() {
            scale = Math.min(scale + 0.2, 3);
            if (!pdfDoc) return;
            renderPage(currentPage);
        });

        zoomOutButton.addEventListener('click', function() {
            scale = Math.max(scale - 0.2, 0.6);
            if (!pdfDoc) return;
            renderPage(currentPage);
        });

        loadPdf();
    });
</script>
@endpush
