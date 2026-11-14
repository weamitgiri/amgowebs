<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Generates responsive, SEO-friendly image variants (banner + thumbnail) from an
 * upload using PHP's GD extension — no external packages required.
 *
 * Files are stored on the public disk under a year/month folder with slugged,
 * search-friendly filenames, e.g. blog/2026/11/my-post-banner.webp.
 */
class ImageService
{
    /** Target sizes (width x height), cover-cropped. */
    private const BANNER = [1600, 900];   // detail hero + Open Graph
    private const THUMB = [640, 400];     // listing cards (matches .thumb 16:10)

    /**
     * Process a blog featured image into banner + thumbnail variants.
     *
     * @return array{banner:string, thumbnail:string}|null  Public URLs, or null if the image can't be read.
     */
    public function processBlogImage(UploadedFile $file, string $slug): ?array
    {
        return $this->process($file, 'blog', $slug);
    }

    /**
     * Store a single in-content image (e.g. uploaded inside the rich-text editor),
     * downscaled to a sensible max width and converted to WebP — no cropping.
     */
    public function storeInline(UploadedFile $file, string $context = 'blog/content'): ?string
    {
        $src = $this->load($file->getRealPath());
        if (! $src) {
            return null;
        }

        $srcW = imagesx($src);
        $srcH = imagesy($src);
        $dstW = min(1400, max($srcW, 1));
        $dstH = (int) round($srcH * ($dstW / max($srcW, 1)));

        $dst = imagecreatetruecolor($dstW, $dstH);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $dstW, $dstH, $srcW, $srcH);
        imagedestroy($src);

        $webp = function_exists('imagewebp');
        $ext = $webp ? 'webp' : 'jpg';
        $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'image';
        $suffix = substr(md5($file->getClientOriginalName().microtime()), 0, 6);
        $relPath = trim($context, '/').'/'.date('Y/m')."/{$name}-{$suffix}.{$ext}";

        ob_start();
        $ok = $webp ? imagewebp($dst, null, 82) : imagejpeg($dst, null, 82);
        $binary = ob_get_clean();
        imagedestroy($dst);

        if (! $ok || $binary === false || $binary === '') {
            return null;
        }

        Storage::disk('public')->put($relPath, $binary);

        return Storage::disk('public')->url($relPath);
    }

    /**
     * @return array{banner:string, thumbnail:string}|null
     */
    public function process(UploadedFile $file, string $context, string $slug): ?array
    {
        $src = $this->load($file->getRealPath());
        if (! $src) {
            return null;
        }

        $srcW = imagesx($src);
        $srcH = imagesy($src);

        $dir = trim($context, '/').'/'.date('Y/m');
        $name = Str::slug($slug) ?: 'image';
        $suffix = substr(md5($file->getClientOriginalName().microtime()), 0, 6);

        $banner = $this->variant($src, $srcW, $srcH, self::BANNER, "{$dir}/{$name}-banner-{$suffix}.webp");
        $thumb = $this->variant($src, $srcW, $srcH, self::THUMB, "{$dir}/{$name}-thumb-{$suffix}.webp");

        imagedestroy($src);

        if (! $banner || ! $thumb) {
            return null;
        }

        return ['banner' => $banner, 'thumbnail' => $thumb];
    }

    /** Load an image resource from a path, detecting the format. */
    private function load(string $path): \GdImage|false
    {
        $info = @getimagesize($path);
        if (! $info) {
            return false;
        }

        return match ($info['mime']) {
            'image/jpeg' => @imagecreatefromjpeg($path),
            'image/png' => @imagecreatefrompng($path),
            'image/gif' => @imagecreatefromgif($path),
            'image/webp' => @imagecreatefromwebp($path),
            default => false,
        };
    }

    /**
     * Create one cover-cropped variant, store it on the public disk, and return its URL.
     *
     * @param  array{0:int,1:int}  $size
     */
    private function variant(\GdImage $src, int $srcW, int $srcH, array $size, string $relPath): ?string
    {
        [$dstW, $dstH] = $size;

        // Don't upscale small images beyond their natural size (keeps them crisp).
        $dstW = min($dstW, max($srcW, 1));
        $dstH = (int) round($dstW * ($size[1] / $size[0]));

        $dst = imagecreatetruecolor($dstW, $dstH);
        imagefill($dst, 0, 0, imagecolorallocate($dst, 15, 17, 38));

        // Cover: fill the target box, cropping the overflowing axis centrally.
        $srcRatio = $srcW / $srcH;
        $dstRatio = $dstW / $dstH;

        if ($srcRatio > $dstRatio) {
            $cropH = $srcH;
            $cropW = (int) round($srcH * $dstRatio);
            $srcX = (int) round(($srcW - $cropW) / 2);
            $srcY = 0;
        } else {
            $cropW = $srcW;
            $cropH = (int) round($srcW / $dstRatio);
            $srcX = 0;
            $srcY = (int) round(($srcH - $cropH) / 2);
        }

        imagecopyresampled($dst, $src, 0, 0, $srcX, $srcY, $dstW, $dstH, $cropW, $cropH);

        ob_start();
        $ok = function_exists('imagewebp') ? imagewebp($dst, null, 82) : imagejpeg($dst, null, 82);
        $binary = ob_get_clean();
        imagedestroy($dst);

        if (! $ok || $binary === false || $binary === '') {
            return null;
        }

        Storage::disk('public')->put($relPath, $binary);

        return Storage::disk('public')->url($relPath);
    }
}
