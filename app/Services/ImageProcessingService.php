<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

class ImageProcessingService
{
    protected $imageManager;
    protected $config;

    public function __construct()
    {
        // Initialize with Imagick driver for better quality
        $this->imageManager = new ImageManager(new Driver());
        $this->config = config('image');
    }

    /**
     * Process and enhance image quality
     *
     * @param string $imagePath
     * @param array $options
     * @return bool
     */
    public function processImage(string $imagePath, array $options = []): bool
    {
        try {
            $path = Storage::path($imagePath);

            // Read image with high-quality settings
            $img = $this->imageManager->read($path);

            // Store original image data
            $originalPath = $path . '.original';
            if (!file_exists($originalPath)) {
                copy($path, $originalPath); // Keep original for reference
            }

            // Get original dimensions
            $width = $img->width();
            $height = $img->height();

            // Merge default config with provided options
            $options = array_merge([
                'max_width' => $this->config['dimensions']['max_width'],
                'max_height' => $this->config['dimensions']['max_height'],
                'quality' => $this->config['quality']['jpeg'],
                'sharpen' => $this->config['optimization']['sharpen'],
                'brightness' => $this->config['optimization']['brightness'],
                'contrast' => $this->config['optimization']['contrast'],
            ], $options);

            // Calculate target dimensions while preserving aspect ratio
            $aspectRatio = $width / $height;
            $targetWidth = $options['max_width'];
            $targetHeight = $options['max_height'];

            if ($aspectRatio > 1) {
                // Landscape image
                $targetHeight = intval($targetWidth / $aspectRatio);
            } else {
                // Portrait image
                $targetWidth = intval($targetHeight * $aspectRatio);
            }

            // Only upscale if the image is smaller than target dimensions
            if ($width < $targetWidth || $height < $targetHeight) {
                // Progressive upscaling for better quality
                $steps = $this->calculateUpscalingSteps($width, $height, $targetWidth, $targetHeight);

                foreach ($steps as $step) {
                    $img->resize($step['width'], $step['height'], function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });

                    // Apply quality enhancement after each step
                    $this->enhanceImageQuality($img);
                }
            }

            // Apply final enhancements
            $this->applyFinalEnhancements($img);

            // Save with maximum quality and optimal format
            $format = $this->determineOptimalFormat($path);
            $this->saveOptimizedImage($img, $path, $format, 100); // Always use maximum quality

            Log::info('Image processed successfully', [
                'path' => $imagePath,
                'original_dimensions' => "{$width}x{$height}",
                'new_dimensions' => "{$img->width()}x{$img->height()}",
                'format' => $format
            ]);

            return true;
        } catch (Exception $e) {
            Log::error('Error processing image: ' . $e->getMessage(), [
                'path' => $imagePath,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Restore original if processing fails
            if (file_exists($originalPath)) {
                copy($originalPath, $path);
            }

            throw $e;
        }
    }

    /**
     * Calculate optimal steps for progressive upscaling
     */
    protected function calculateUpscalingSteps($currentWidth, $currentHeight, $targetWidth, $targetHeight): array
    {
        $steps = [];
        $factor = 1.5; // Maximum scale factor per step

        while ($currentWidth < $targetWidth || $currentHeight < $targetHeight) {
            $nextWidth = min($currentWidth * $factor, $targetWidth);
            $nextHeight = min($currentHeight * $factor, $targetHeight);

            $steps[] = [
                'width' => (int)$nextWidth,
                'height' => (int)$nextHeight
            ];

            if ($nextWidth == $targetWidth && $nextHeight == $targetHeight) {
                break;
            }

            $currentWidth = $nextWidth;
            $currentHeight = $nextHeight;
        }

        return $steps;
    }

    /**
     * Apply advanced upscaling techniques
     */
    protected function applyAdvancedUpscaling($img): void
    {
        // Apply advanced sharpening
        $img->sharpen(15);

        // Apply noise reduction
        $this->applyNoiseReduction($img);

        // Apply edge enhancement
        $this->applyEdgeEnhancement($img);
    }

    /**
     * Apply noise reduction
     */
    protected function applyNoiseReduction($img): void
    {
        // Apply gaussian blur with small radius to reduce noise
        $img->blur(0.5);
    }

    /**
     * Apply edge enhancement
     */
    protected function applyEdgeEnhancement($img): void
    {
        $img->sharpen(20);
    }

    /**
     * Enhance image quality during processing
     */
    protected function enhanceImageQuality($img): void
    {
        // Apply subtle sharpening
        $img->sharpen(10);

        // Adjust contrast and brightness
        $img->brightness(5)
            ->contrast(10);
    }

    /**
     * Apply final enhancements before saving
     */
    protected function applyFinalEnhancements($img): void
    {
        // Apply final quality enhancements
        $img->brightness(2)
            ->contrast(15)
            ->sharpen(25);

        // Apply color enhancement
        $this->enhanceColors($img);
    }

    /**
     * Enhance image colors
     */
    protected function enhanceColors($img): void
    {
        $img->contrast(15)
            ->gamma(1.1); // Slightly increase gamma for better color perception
    }

    /**
     * Determine the optimal format based on the image
     */
    protected function determineOptimalFormat(string $path): string
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        // If it's already a JPEG or PNG, keep the format
        if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
            return $extension;
        }

        // Default to JPEG for best quality/size ratio
        return 'jpg';
    }

    /**
     * Save the image with optimal settings
     */
    protected function saveOptimizedImage($img, string $path, string $format, int $quality): void
    {
        switch ($format) {
            case 'png':
                $img->toPng(0)->save($path); // No compression for PNG
                break;
            case 'webp':
                $img->toWebp(100)->save($path); // Maximum quality for WebP
                break;
            default:
                $img->toJpeg(100)->save($path); // Maximum quality for JPEG
        }
    }
}

