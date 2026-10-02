<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class ImageService
{
    /** Store an uploaded image (resized + converted to WebP) and return its relative path. */
    public function store(UploadedFile $file, string $dir, int $maxWidth = 1600): string
    {
        $name = Str::uuid().'.webp';
        $path = trim($dir, '/').'/'.$name;

        try {
            $image = Image::read($file->getRealPath());
            if ($image->width() > $maxWidth) {
                $image->scaleDown(width: $maxWidth);
            }
            Storage::disk('public')->put($path, (string) $image->toWebp(85));
        } catch (\Throwable) {
            // Fallback: store the original untouched.
            $path = $file->store(trim($dir, '/'), 'public');
        }

        return $path;
    }

    public function delete(?string $path): void
    {
        if ($path && ! str_starts_with($path, 'http')) {
            Storage::disk('public')->delete($path);
        }
    }
}
