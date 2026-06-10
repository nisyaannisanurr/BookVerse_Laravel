<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

class FileUploadService
{
    /**
     * Upload image with MIME validation
     */
    public function uploadImage(UploadedFile $file, string $targetDir): string|false
    {
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        if ($file->getSize() > $maxSize) {
            return false;
        }

        if (!in_array($file->getMimeType(), $allowedMimes, true)) {
            return false;
        }

        $extMap = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp'
        ];
        $ext = $extMap[$file->getMimeType()];
        $filename = bin2hex(random_bytes(16)) . '.' . $ext;

        $fullDir = public_path('uploads/' . trim($targetDir, '/'));
        if (!is_dir($fullDir)) {
            mkdir($fullDir, 0755, true);
        }

        $file->move($fullDir, $filename);

        return $filename;
    }

    /**
     * Delete uploaded file
     */
    public function deleteUpload(string $targetDir, ?string $filename): bool
    {
        if (!$filename) return false;

        $fullPath = public_path('uploads/' . trim($targetDir, '/') . '/' . $filename);
        if (File::exists($fullPath)) {
            return File::delete($fullPath);
        }
        return false;
    }
}
