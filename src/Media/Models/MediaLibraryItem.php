<?php

namespace RalphJSmit\Filament\MediaLibrary\Media\Models;

use Filament\Models\Contracts\HasName;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe\DataMapping\Format;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RalphJSmit\Filament\MediaLibrary\Database\Factories\Media\MediaLibraryFactory;
use RalphJSmit\Filament\MediaLibrary\Exceptions\MediaLibraryException;
use RalphJSmit\Filament\MediaLibrary\FilamentMediaLibrary;
use RalphJSmit\Filament\MediaLibrary\Media\DataTransferObjects\MediaItemMeta;
use RalphJSmit\Filament\MediaLibrary\Support\Concerns\HasContentMedia;
use Spatie\Image\Image;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\PdfToImage\Pdf;

class MediaLibraryItem extends Model implements HasMedia
{
    use HasContentMedia;
    use HasFactory;

    protected $casts = [];

    protected $guarded = [];

    protected $table = 'filament_media_library';

    protected $with = [
        'user',
    ];

    protected static function newFactory(): MediaLibraryFactory
    {
        return MediaLibraryFactory::new();
    }

    public function getMorphClass(): string
    {
        return 'filament_media_library_item';
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(FilamentMediaLibrary::get()->getModelFolder(), 'folder_id');
    }

    public static function addUpload(UploadedFile $uploadedFile, ?MediaLibraryFolder $mediaLibraryFolder = null, array $attributes = []): static
    {
        return DB::transaction(function () use ($attributes, $mediaLibraryFolder, $uploadedFile) {
            /** @var MediaLibraryItem $media */
            $media = FilamentMediaLibrary::get()->getModelItem()::make([
                'folder_id' => $mediaLibraryFolder?->getKey(),
                ...$attributes,
            ]);

            // Get full user object instead of just `auth()->id()`, so
            // that *if* there is a `MorphTo`, we know the user type.
            $user = Filament::getCurrentOrDefaultPanel()
                ? Filament::auth()->user()
                : auth()->user();

            if ($user) {
                // Use relationship `associate()` instead of directly inserting
                // relationship attribute, in order to support user `MorphTo`.
                $media->user()->associate($user);
            }

            $media->save();

            $media->addOrReplaceUpload($uploadedFile);

            throw_if($media->getMedia($media->getMediaLibraryCollectionName())->isEmpty(), new MediaLibraryException("Media collection [{$media->getMediaLibraryCollectionName()}] for media library [{$media->getKey()}] is empty after adding an upload."));

            return $media;
        });
    }

    public function addOrReplaceUpload(UploadedFile $uploadedFile): static
    {
        return DB::transaction(function () use ($uploadedFile) {
            if ($uploadedFile->getClientOriginalName() === 'blob') {
                $fileName = 'blob.' . $uploadedFile->getClientOriginalExtension();
            } else {
                // Prevent filenames like example.jpeg.jpeg.
                $fileName = Str::of($uploadedFile->getClientOriginalName())
                    ->replace('.' . $uploadedFile->getClientOriginalExtension() . '.' . $uploadedFile->getClientOriginalExtension(), '.' . $uploadedFile->getClientOriginalExtension());
            }

            try {
                $disk = config('livewire.temporary_file_upload.disk', 'default');

                $pathToTemporaryLivewireFile = (string) Str::of($uploadedFile->getRealPath())
                    ->after(Storage::disk($disk)->path('/'))
                    ->ltrim('/');

                $this
                    ->addMediaFromDisk($pathToTemporaryLivewireFile, $disk)
                    ->usingName($fileName)
                    ->usingFileName($fileName)
                    ->toMediaCollection($this->getMediaLibraryCollectionName())
                    ->save();
            } catch (FileCannotBeAdded $e) {
                $this
                    ->addMedia($uploadedFile)
                    ->usingName($fileName)
                    ->usingFileName($fileName)
                    ->toMediaCollection($this->getMediaLibraryCollectionName())
                    ->save();
            }

            Cache::forget($this->getParsedItemCacheKey());

            return $this;
        });
    }

    public function getItem(?string $collection = null): Media
    {
        return $this->getFirstMedia($collection ?? $this->getMediaLibraryCollectionName());
    }

    public function user(): BelongsTo
    {
        $userModel = (Filament::getCurrentOrDefaultPanel() ? Filament::auth() : auth())->getProvider()->getModel() ?? '\App\Models\User';

        return $this->belongsTo($userModel, 'uploaded_by_user_id');
    }

    public function getParsedItemCacheKey(): string
    {
        return "media-library-item.{$this->getKey()}.parsed_item";
    }

    public function getMeta(bool $parseItem = false): MediaItemMeta
    {
        $item = $this->getItem();

        if ($parseItem) {
            $parsedItem = Cache::rememberForever($this->getParsedItemCacheKey(), function () use ($item) {
                /**
                 * @var ?Image $itemAsImage
                 * @var ?Pdf $itemAsPdf
                 * @var ?Format $itemAsVideo
                 */
                [$itemAsImage, $itemAsPdf, $itemAsVideo] = $this->parseItem($item);

                return [
                    'image_width' => $itemAsImage?->getWidth(),
                    'image_height' => $itemAsImage?->getHeight(),
                    // The `spatie/pdf-to-image` V3 uses `->pageCount()`, whereas `spatie/pdf-to-image` V2 uses `->getNumberOfPages()`.
                    'pdf_number_of_pages' => $itemAsPdf ? (method_exists($itemAsPdf, 'pageCount') ? $itemAsPdf->pageCount() : $itemAsPdf->getNumberOfPages()) : null,
                    'video_duration' => $this->getVideoDuration($itemAsVideo),
                ];
            });
        }

        if ($this->user) {
            $uploadedByName = null;

            if ($this->user instanceof HasName) {
                $uploadedByName ??= $this->user->getFilamentName();
            }

            if ($this->user->getAttributes()['name'] ?? null) {
                $uploadedByName ??= $this->user->name;
            }

            if (($this->user->getAttributes()['first_name'] ?? null) || ($this->user->getAttributes()['last_name'] ?? null)) {
                $uploadedByName ??= implode(' ', array_filter([$this->user->first_name, $this->user->last_name]));
            }
        }

        return new MediaItemMeta(
            name: $item->name,
            human_readable_size: $item->human_readable_size ?? '',
            uploaded_by_name: $uploadedByName ?? '',
            uploaded_at: $this->created_at->locale(config('app.locale'))->calendar() ?? '',
            folder_name: $this->folder?->name ?? __('filament-media-library::translations.root-folder'),
            width: $parsedItem['image_width'] ?? null,
            height: $parsedItem['image_height'] ?? null,
            url: $this->getFirstAvailableUrl(),
            full_url: $this->getUrl(),
            altText: $this->alt_text,
            caption: $this->caption,
            id: $this->getKey(),
            pdf_nr_of_pages: $parsedItem['pdf_number_of_pages'] ?? null,
            video_duration: $parsedItem['video_duration'] ?? null,
        );
    }

    protected function getFirstAvailableUrl(): string
    {
        $media = $this->getItem();

        foreach (FilamentMediaLibrary::get()->getFirstAvailableUrlConversions() as $conversionName) {
            if (! $media->hasGeneratedConversion($conversionName)) {
                continue;
            }

            return $this->getUrl($conversionName);
        }

        return $this->getUrl();
    }

    public function getUrl(string $conversionName = ''): string
    {
        $media = $this->getItem();

        return match (FilamentMediaLibrary::get()->getDiskVisibility()) {
            'public' => $media->getUrl($conversionName),
            'private' => $media->getTemporaryUrl(now()->addMinutes(30), $conversionName),
        };
    }

    protected function parseItem(Media $media): array
    {
        return [
            $this->getItemAsImage($media),
            $this->getItemAsPdf($media),
            $this->getItemAsVideo($media),
        ];
    }

    protected function getItemAsImage(Media $media): ?Image
    {
        if (! in_array($media->mime_type, ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/bmp'])) {
            return null;
        }

        if ($media->getDiskDriverName() !== 'local') {
            return null;
        }

        return rescue(fn () => Image::load($media->getPath()), null, false);
    }

    protected function getItemAsPdf(Media $media): ?Pdf
    {
        if ($media->mime_type !== 'application/pdf') {
            return null;
        }

        return rescue(fn () => new Pdf($media->getPath()), null, false);
    }

    protected function getItemAsVideo(Media $media): ?Format
    {
        if (! Str::startsWith($media->mime_type, 'video/')) {
            return null;
        }

        return rescue(function () use ($media) {
            $ffmpeg = FFMpeg::create([
                'ffmpeg.binaries' => config('media-library.ffmpeg_path'),
                'ffprobe.binaries' => config('media-library.ffprobe_path'),
            ]);

            return $ffmpeg->getFFProbe()->format($media->getPath());
        }, null, false);
    }

    protected function getVideoDuration(?Format $video): ?string
    {
        if (! $video) {
            return null;
        }

        return rescue(function () use ($video) {
            $durationInSeconds = $video->get('duration');

            $fullHours = floor($durationInSeconds / 3600);
            $fullMinutes = floor(($durationInSeconds - 3600 * $fullHours) / 60);
            $fullSeconds = round($durationInSeconds - 3600 * $fullHours - 60 * $fullMinutes);

            if ($fullHours <= 9) {
                $fullHours = '0' . $fullHours;
            }

            if ($fullMinutes <= 9) {
                $fullMinutes = '0' . $fullMinutes;
            }

            if ($fullSeconds <= 9) {
                $fullSeconds = '0' . $fullSeconds;
            }

            $duration = "{$fullMinutes}:{$fullSeconds}";

            // Only prepend hours if the video is longer than 1 hour.
            if ($fullHours !== '00') {
                $duration = "{$fullHours}:{$duration}";
            }

            return $duration;
        }, null, false);
    }
}
