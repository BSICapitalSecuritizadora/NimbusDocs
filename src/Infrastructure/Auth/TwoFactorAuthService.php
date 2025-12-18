<?php

declare(strict_types=1);

namespace App\Infrastructure\Auth;

/**
 * Two-Factor Authentication Service using TOTP (RFC 6238)
 * 
 * This is a simple implementation that doesn't require external dependencies.
 * It uses HMAC-SHA1 as per the TOTP standard.
 */
class TwoFactorAuthService
{
    private const SECRET_LENGTH = 16;
    private const CODE_LENGTH = 6;
    private const TIME_STEP = 30;
    private const BASE32_ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    /**
     * Generate a new random secret
     */
    public function generateSecret(): string
    {
        $secret = '';
        $randomBytes = random_bytes(self::SECRET_LENGTH);
        
        for ($i = 0; $i < self::SECRET_LENGTH; $i++) {
            $secret .= self::BASE32_ALPHABET[ord($randomBytes[$i]) % 32];
        }
        
        return $secret;
    }

    /**
     * Generate QR code URL for authenticator apps
     */
    public function getQrCodeUrl(string $secret, string $email, string $issuer = 'NimbusDocs'): string
    {
        $otpAuthUrl = sprintf(
            'otpauth://totp/%s:%s?secret=%s&issuer=%s&algorithm=SHA1&digits=%d&period=%d',
            rawurlencode($issuer),
            rawurlencode($email),
            $secret,
            rawurlencode($issuer),
            self::CODE_LENGTH,
            self::TIME_STEP
        );

        // Use Google Charts API to generate QR code
        return sprintf(
            'https://chart.googleapis.com/chart?cht=qr&chs=200x200&chl=%s&choe=UTF-8',
            urlencode($otpAuthUrl)
        );
    }

    /**
     * Verify a TOTP code
     */
    public function verify(string $secret, string $code, int $window = 1): bool
    {
        if (strlen($code) !== self::CODE_LENGTH || !ctype_digit($code)) {
            return false;
        }

        $timestamp = time();
        $timeSlice = floor($timestamp / self::TIME_STEP);

        // Check current time slice and surrounding window
        for ($i = -$window; $i <= $window; $i++) {
            $calculatedCode = $this->generateCode($secret, (int) ($timeSlice + $i));
            
            if (hash_equals($calculatedCode, $code)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate a TOTP code for a given time slice
     */
    private function generateCode(string $secret, int $timeSlice): string
    {
        // Decode Base32 secret
        $decodedSecret = $this->base32Decode($secret);
        
        // Pack time slice as 8-byte big-endian
        $time = pack('N*', 0, $timeSlice);
        
        // Calculate HMAC-SHA1
        $hmac = hash_hmac('sha1', $time, $decodedSecret, true);
        
        // Get offset from last nibble
        $offset = ord($hmac[19]) & 0x0f;
        
        // Extract 4 bytes from HMAC starting at offset
        $binary = (ord($hmac[$offset]) & 0x7f) << 24
            | (ord($hmac[$offset + 1]) & 0xff) << 16
            | (ord($hmac[$offset + 2]) & 0xff) << 8
            | (ord($hmac[$offset + 3]) & 0xff);
        
        // Generate code
        $code = $binary % (10 ** self::CODE_LENGTH);
        
        return str_pad((string) $code, self::CODE_LENGTH, '0', STR_PAD_LEFT);
    }

    /**
     * Decode a Base32 encoded string
     */
    private function base32Decode(string $input): string
    {
        $input = strtoupper($input);
        $input = str_replace('=', '', $input);
        
        $output = '';
        $buffer = 0;
        $bitsLeft = 0;
        
        for ($i = 0; $i < strlen($input); $i++) {
            $char = $input[$i];
            $value = strpos(self::BASE32_ALPHABET, $char);
            
            if ($value === false) {
                continue;
            }
            
            $buffer = ($buffer << 5) | $value;
            $bitsLeft += 5;
            
            if ($bitsLeft >= 8) {
                $bitsLeft -= 8;
                $output .= chr(($buffer >> $bitsLeft) & 0xff);
            }
        }
        
        return $output;
    }

    /**
     * Get current TOTP code (for testing/debug purposes)
     */
    public function getCurrentCode(string $secret): string
    {
        $timeSlice = (int) floor(time() / self::TIME_STEP);
        return $this->generateCode($secret, $timeSlice);
    }
}
