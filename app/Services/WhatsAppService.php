<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Send an OTP message via WhatsApp using Fonnte API
     *
     * @param string $target WhatsApp number (e.g., 08123456789)
     * @param string $otpCode 6-digit OTP code
     * @return bool True if successful, false otherwise
     */
    public function sendOTP(string $target, string $otpCode): bool
    {
        $token = env('FONNTE_TOKEN');
        
        // Cek jika token kosong (simulasi mode)
        if (empty($token)) {
            Log::info("=== WHATSAPP SIMULATION ===");
            Log::info("To: {$target}");
            Log::info("Message: [BookVerse Security] JANGAN BERIKAN KODE INI KE SIAPAPUN. Kode OTP Anda adalah: *{$otpCode}*. Berlaku selama 10 menit.");
            Log::info("===========================");
            return true;
        }

        try {
            $message = "*[BookVerse Security]*\nJANGAN BERIKAN KODE INI KE SIAPAPUN.\n\nKode OTP Anda adalah: *{$otpCode}*\n\nKode ini berlaku selama 10 menit.";
            
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->post('https://api.fonnte.com/send', [
                'target' => $target,
                'message' => $message,
                'countryCode' => '62', // Default Indonesia
            ]);

            if ($response->successful()) {
                return true;
            } else {
                Log::error('Fonnte API Error: ' . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            Log::error('WhatsAppService Exception: ' . $e->getMessage());
            return false;
        }
    }
}
