<?php

namespace App\Services;

class MfaService
{
    /**
     * Generate a TOTP secret for the user
     */
    public static function generateSecret(): string
    {
        return self::base32_encode(random_bytes(10));
    }

    /**
     * Get the provisioning URI for QR code generation
     */
    public static function getProvisioningUri($email, $appName, $secret): string
    {
        return sprintf(
            'otpauth://totp/%s%%3A%s?secret=%s&issuer=%s',
            urlencode($appName),
            urlencode($email),
            $secret,
            urlencode($appName)
        );
    }

    /**
     * Verify a TOTP code
     */
    public static function verifyCode($secret, $code, $discrepancy = 1): bool
    {
        $code = (string) $code;
        
        // Generate codes for current and surrounding time windows
        for ($i = -$discrepancy; $i <= $discrepancy; $i++) {
            $timeSlice = floor(time() / 30) + $i;
            $generatedCode = self::generateCode($secret, $timeSlice);
            
            if ($generatedCode === $code) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Generate a TOTP code
     */
    private static function generateCode($secret, $timeSlice): string
    {
        $secretBinary = self::base32_decode($secret);
        
        $time = pack('N', $timeSlice);
        if (strlen($time) < 8) {
            $time = str_pad($time, 8, chr(0), STR_PAD_LEFT);
        }
        
        $hmac = hash_hmac('sha1', $time, $secretBinary, true);
        
        $offset = ord($hmac[19]) & 0xf;
        $fourBytes = unpack('N', substr($hmac, $offset, 4))[1];
        $fourBytes &= 0x7fffffff;
        $code = $fourBytes % 1000000;
        
        return str_pad($code, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Base32 encode
     */
    private static function base32_encode($data): string
    {
        $base32Alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $output = '';
        $v = 0;
        $vbits = 0;
        
        for ($i = 0; $i < strlen($data); $i++) {
            $v = ($v << 8) | ord($data[$i]);
            $vbits += 8;
            
            while ($vbits >= 5) {
                $vbits -= 5;
                $output .= $base32Alphabet[($v >> $vbits) & 31];
            }
        }
        
        if ($vbits > 0) {
            $output .= $base32Alphabet[($v << (5 - $vbits)) & 31];
        }
        
        return $output;
    }

    /**
     * Base32 decode
     */
    private static function base32_decode($data): string
    {
        $base32Alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $data = strtoupper($data);
        $output = '';
        $v = 0;
        $vbits = 0;
        
        for ($i = 0; $i < strlen($data); $i++) {
            $v = ($v << 5) | strpos($base32Alphabet, $data[$i]);
            $vbits += 5;
            
            if ($vbits >= 8) {
                $vbits -= 8;
                $output .= chr(($v >> $vbits) & 255);
            }
        }
        
        return $output;
    }
}
