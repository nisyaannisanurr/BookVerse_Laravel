<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class FileUploadService
{
    /**
     * Upload image with MIME validation and compression
     */
    public function uploadImage(UploadedFile $file, string $targetDir): string|false
    {
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
        $maxSize = 10 * 1024 * 1024; // 10MB (dibesarkan sedikit karena kita akan kompres)

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

        try {
            // Compress and Resize using Intervention Image v3
            $manager = new ImageManager(new Driver());
            $image = $manager->read($file->getPathname());
            
            // Resize if width > 1200px
            if ($image->width() > 1200) {
                $image->scaleDown(width: 1200);
            }
            
            // Save with 80% quality
            $image->save($fullDir . '/' . $filename, quality: 80);
        } catch (\Exception $e) {
            // Fallback to standard move if GD fails or exception occurs
            $file->move($fullDir, $filename);
        }

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
