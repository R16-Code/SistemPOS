<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageOptimizer
{
    /**
     * Optimize, resize, and convert an uploaded image to WebP format.
     *
     * @param  string  $directory  Storage subdirectory (e.g. 'products', 'settings')
     * @param  int  $maxWidth  Maximum allowed width in pixels
     * @param  int  $quality  Compression quality (1-100)
     * @return string Stored file path relative to public disk
     */
    public static function optimizeAndStore(
        UploadedFile $file,
        string $directory = 'products',
        int $maxWidth = 800,
        int $quality = 85
    ): string {
        // If GD extension is not loaded, fallback to standard store
        if (! extension_loaded('gd')) {
            return $file->store($directory, 'public');
        }

        $realPath = $file->getRealPath();
        if (! $realPath || ! file_exists($realPath)) {
            return $file->store($directory, 'public');
        }

        $imageInfo = @getimagesize($realPath);
        if (! $imageInfo) {
            return $file->store($directory, 'public');
        }

        [$origWidth, $origHeight, $imageType] = $imageInfo;

        // Load image resource based on mime type
        $sourceImage = match ($imageType) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($realPath),
            IMAGETYPE_PNG => @imagecreatefrompng($realPath),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($realPath) : null,
            default => null,
        };

        if (! $sourceImage) {
            return $file->store($directory, 'public');
        }

        // Calculate proportional dimensions
        if ($origWidth > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = (int) round(($origHeight / $origWidth) * $maxWidth);
        } else {
            $newWidth = $origWidth;
            $newHeight = $origHeight;
        }

        // Create target canvas
        $targetImage = imagecreatetruecolor($newWidth, $newHeight);

        // Preserve alpha transparency for PNG & WebP
        imagealphablending($targetImage, false);
        imagesavealpha($targetImage, true);
        $transparent = imagecolorallocatealpha($targetImage, 255, 255, 255, 127);
        imagefilledrectangle($targetImage, 0, 0, $newWidth, $newHeight, $transparent);

        // Resample with high quality interpolation
        imagecopyresampled(
            $targetImage,
            $sourceImage,
            0, 0, 0, 0,
            $newWidth,
            $newHeight,
            $origWidth,
            $origHeight
        );

        // Generate clean webp filename
        $filename = Str::random(40).'.webp';
        $relativeDir = trim($directory, '/');
        $targetDir = Storage::disk('public')->path($relativeDir);

        if (! file_exists($targetDir)) {
            @mkdir($targetDir, 0755, true);
        }

        $targetPath = $targetDir.DIRECTORY_SEPARATOR.$filename;

        // Save as WebP if supported, otherwise fallback to JPEG
        $saved = false;
        if (function_exists('imagewebp')) {
            $saved = @imagewebp($targetImage, $targetPath, $quality);
        }

        if (! $saved) {
            $filename = Str::random(40).'.jpg';
            $targetPath = $targetDir.DIRECTORY_SEPARATOR.$filename;
            $saved = @imagejpeg($targetImage, $targetPath, $quality);
        }

        // Free GD memory
        imagedestroy($sourceImage);
        imagedestroy($targetImage);

        if ($saved && file_exists($targetPath)) {
            return $relativeDir.'/'.$filename;
        }

        return $file->store($directory, 'public');
    }
}
