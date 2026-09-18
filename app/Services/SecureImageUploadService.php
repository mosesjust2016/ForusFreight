<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Validates and stores an uploaded image safely, regardless of how much the
 * caller already trusts Laravel's `image`/`mimes` validation rules.
 *
 * Those rules check the file's MIME type, which is not the same as
 * confirming the file is *only* image data. A "polyglot" file — one that
 * starts with valid image bytes so it passes MIME sniffing, but has
 * executable code appended or embedded after the image data — can pass
 * `image|mimes:...` validation outright. If that file is ever served from a
 * misconfigured path, included by some other code, or the server is
 * reconfigured later, the appended payload can execute.
 *
 * This class closes that gap two ways:
 *   1. It decodes the file with getimagesize() + the matching GD
 *      imagecreatefrom*() call — a real decode, not a header/MIME guess —
 *      and rejects anything that isn't a genuine, fully-parseable image.
 *   2. It re-encodes the image from the decoded pixel data into a brand
 *      new file. Re-encoding only ever writes back what GD actually
 *      decoded, so any non-image bytes the original upload contained
 *      (appended, embedded, wherever) cannot survive into the stored file
 *      — there's nothing left to carry them.
 *
 * The stored filename is always freshly randomised here too, never derived
 * from the client-supplied filename or extension.
 */
class SecureImageUploadService
{
    /**
     * PHP's IMAGETYPE_* constant => how to decode/re-encode/name that format.
     */
    private const FORMATS = [
        IMAGETYPE_JPEG => ['ext' => 'jpg', 'create' => 'imagecreatefromjpeg', 'save' => 'imagejpeg', 'quality' => 85],
        IMAGETYPE_PNG  => ['ext' => 'png', 'create' => 'imagecreatefrompng', 'save' => 'imagepng'],
        IMAGETYPE_GIF  => ['ext' => 'gif', 'create' => 'imagecreatefromgif', 'save' => 'imagegif'],
        IMAGETYPE_WEBP => ['ext' => 'webp', 'create' => 'imagecreatefromwebp', 'save' => 'imagewebp', 'quality' => 85],
    ];

    /**
     * Validate, re-encode, and store an uploaded image on the given disk.
     *
     * @param  UploadedFile  $file
     * @param  string  $directory  Destination directory on the disk (no leading/trailing slash needed).
     * @param  string  $disk  Filesystem disk name, defaults to the app's public disk.
     * @return string  The stored path, relative to the disk root.
     *
     * @throws RuntimeException if the file is not a genuine, supported image.
     */
    public static function store(UploadedFile $file, string $directory, string $disk = 'public'): string
    {
        $realPath = $file->getRealPath();

        // A real decode of the file's header structure — not a MIME guess.
        // False for anything that isn't a genuine image, including a
        // polyglot whose leading bytes don't form a complete valid image.
        $info = $realPath ? @getimagesize($realPath) : false;

        if ($info === false || !isset(self::FORMATS[$info[2]])) {
            throw new RuntimeException('The uploaded file is not a valid image.');
        }

        $format = self::FORMATS[$info[2]];
        $create = $format['create'];

        if (!function_exists($create)) {
            throw new RuntimeException('This image format is not supported on this server.');
        }

        $image = @$create($realPath);

        if ($image === false) {
            throw new RuntimeException('The uploaded file could not be decoded as an image.');
        }

        $directory = trim($directory, '/');
        $filename = Str::random(40) . '.' . $format['ext'];
        $tmpPath = tempnam(sys_get_temp_dir(), 'secimg_');

        try {
            $save = $format['save'];
            $saved = isset($format['quality'])
                ? $save($image, $tmpPath, $format['quality'])
                : $save($image, $tmpPath);

            if (!$saved) {
                throw new RuntimeException('The image could not be processed.');
            }

            $stream = fopen($tmpPath, 'r');
            Storage::disk($disk)->put("{$directory}/{$filename}", $stream);
            fclose($stream);
        } finally {
            // imagedestroy() is a no-op as of PHP 8.0 (deprecated in 8.5) —
            // GD-backed GdImage objects are freed by ordinary refcounting.
            @unlink($tmpPath);
        }

        return "{$directory}/{$filename}";
    }
}
