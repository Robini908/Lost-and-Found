<?php

namespace App\Observers;

use App\Models\LostItemImage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\Encoders\WebpEncoder;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Illuminate\Support\Facades\Log;

class LostItemImageObserver
{
    public function saved(LostItemImage $lostItemImage)
    {
        if ($lostItemImage->image_path) {
            $this->optimizeImage($lostItemImage->image_path);
        }
    }

    protected function optimizeImage($imagePath)
    {
        $fullPath = storage_path('app/public/' . $imagePath);

        if (!file_exists($fullPath) || !is_readable($fullPath)) {
            Log::error("Image not found or unreadable: {$fullPath}");
            return;
        }

        try {
            // Initialize ImageManager
            $manager = new ImageManager(new Driver());
            $image = $manager->read($fullPath);

            // Determine image format
            $format = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

            // Apply sharpening for better clarity
            $image->sharpen(10);

            // Encode and compress based on format
            switch ($format) {
                case 'jpg':
                case 'jpeg':
                    $image->encode(new JpegEncoder(100)); // Pass quality as a positional argument
                    break;
                case 'png':
                    $image->encode(new PngEncoder(9)); // Pass compression level as a positional argument
                    break;
                case 'webp':
                    $image->encode(new WebpEncoder(85)); // Pass quality as a positional argument
                    break;
                default:
                    Log::warning("Unsupported image format: {$format}");
                    return;
            }

            // Save optimized image
            $image->save($fullPath);

            // Optimize image using Spatie Image Optimizer
            $optimizerChain = OptimizerChainFactory::create();
            $optimizerChain->optimize($fullPath);

            Log::info("Image optimized successfully: {$fullPath}");
        } catch (\Exception $e) {
            Log::error("Image optimization failed: {$e->getMessage()}");
        }
    }
}
