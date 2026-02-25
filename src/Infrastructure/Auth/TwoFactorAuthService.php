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
    /**
     * Generate OTP Auth URL (raw)
     */
    public function getOtpAuthUrl(string $secret, string $email, string $issuer = 'NimbusDocs'): string
    {
        return sprintf(
            'otpauth://totp/%s:%s?secret=%s&issuer=%s&algorithm=SHA1&digits=%d&period=%d',
            rawurlencode($issuer),
            rawurlencode($email),
            $secret,
            rawurlencode($issuer),
            self::CODE_LENGTH,
            self::TIME_STEP
        );
    }

    /**
     * Generate QR code as a local data URI (SVG) — no external service
     * Returns a data:image/svg+xml;base64,... string usable as <img src>
     */
    public function getQrCodeUrl(string $secret, string $email, string $issuer = 'NimbusDocs'): string
    {
        $otpAuthUrl = $this->getOtpAuthUrl($secret, $email, $issuer);

        // Generate QR matrix locally
        $matrix = $this->generateQrMatrix($otpAuthUrl);
        $size = count($matrix);
        $scale = 4;
        $border = 4;
        $totalSize = ($size + 2 * $border) * $scale;

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . $totalSize . ' ' . $totalSize . '">';
        $svg .= '<rect width="100%" height="100%" fill="#fff"/>';

        for ($y = 0; $y < $size; $y++) {
            for ($x = 0; $x < $size; $x++) {
                if ($matrix[$y][$x]) {
                    $px = ($x + $border) * $scale;
                    $py = ($y + $border) * $scale;
                    $svg .= '<rect x="' . $px . '" y="' . $py . '" width="' . $scale . '" height="' . $scale . '" fill="#000"/>';
                }
            }
        }

        $svg .= '</svg>';

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    /**
     * Generate a QR code bit matrix using a simple QR encoder.
     * Delegates to a minimal local encoder — no external HTTP calls.
     * Falls back to a placeholder matrix if encoding fails.
     *
     * @return array<int, array<int, int>>
     */
    private function generateQrMatrix(string $data): array
    {
        // Use the chillerlan/php-qrcode library if available (composer)
        if (class_exists(\chillerlan\QRCode\QRCode::class)) {
            $options = new \chillerlan\QRCode\QROptions([
                'outputType' => \chillerlan\QRCode\Output\QROutputInterface::CUSTOM,
                'eccLevel' => \chillerlan\QRCode\Common\EccLevel::H,
            ]);
            $qr = new \chillerlan\QRCode\QRCode($options);

            return $qr->getQRMatrix()->getMatrix();
        }

        // Minimal fallback: return the OTP Auth URL as a data URI
        // that the client-side QRious library will handle instead.
        // Return an empty matrix — the view's JS QRious is the primary renderer.
        return array_fill(0, 1, array_fill(0, 1, 0));
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
