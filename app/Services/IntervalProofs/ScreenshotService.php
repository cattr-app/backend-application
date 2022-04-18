<?php

namespace App\Services\IntervalProofs;

use App\Contracts\IntervalProofProvider;
use App\Jobs\GenerateScreenshotThumbnail;
use App\Models\TimeInterval;
use Image;
use Intervention\Image\Constraint;
use Storage;

class ScreenshotService implements IntervalProofProvider
{
    protected const FILE_FORMAT = 'jpg';
    protected const PARENT_FOLDER = 'screenshots/';
    protected const THUMBS_FOLDER = 'thumbs/';
    protected const THUMB_WIDTH = 280;

    public static function getFullPath(): string
    {
        $fileSystemPath = config('filesystems.default');
        return storage_path(config("filesystems.disks.$fileSystemPath.root"));
    }

    public static function store(mixed $data, TimeInterval $interval): void
    {
        if (!Storage::exists(self::PARENT_FOLDER)) {
            Storage::makeDirectory(self::PARENT_FOLDER);
        }

        $path = is_string($data) ? $data : $data->path();

        $image = Image::make($path);

        Storage::put(self::getScreenshotPath($interval), (string)$image->encode(self::FILE_FORMAT));

        GenerateScreenshotThumbnail::dispatch($interval);
    }

    public static function exists(TimeInterval $interval): bool
    {
        return Storage::exists(self::getScreenshotPath($interval));
    }

    private static function getScreenshotPath(TimeInterval $interval): string
    {
        return self::PARENT_FOLDER . hash('sha256', optional($interval)->id ?: $interval) . '.' . self::FILE_FORMAT;
    }

    public static function get(TimeInterval $interval): mixed
    {
        // TODO: Implement get() method.
    }

    public static function createThumbnail(TimeInterval $interval): void
    {
        if (!Storage::exists(self::PARENT_FOLDER . self::THUMBS_FOLDER)) {
            Storage::makeDirectory(self::PARENT_FOLDER . self::THUMBS_FOLDER);
        }

        $image = Image::make(Storage::path(self::getScreenshotPath($interval)));

        $thumb = $image->resize(
            self::THUMB_WIDTH,
            null,
            static fn(Constraint $constraint) => $constraint->aspectRatio()
        );

        Storage::put(self::getThumbPath($interval), (string)$thumb->encode(self::FILE_FORMAT));
    }

    private static function getThumbPath(TimeInterval $interval): string
    {
        return self::PARENT_FOLDER . self::THUMBS_FOLDER . hash(
            'sha256',
            optional($interval)->id ?: $interval
        ) . '.' . self::FILE_FORMAT;
    }

    public static function destroy(TimeInterval $interval): void
    {
        Storage::delete(self::getScreenshotPath($interval));
        Storage::delete(self::getThumbPath($interval));
    }

    public static function getMask(): int
    {
        return 1 << 0;
    }

    public static function getName(): string
    {
        return 'screenshots';
    }
}
