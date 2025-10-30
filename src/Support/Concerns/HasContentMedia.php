<?php

namespace RalphJSmit\Filament\MediaLibrary\Support\Concerns;

use RalphJSmit\Filament\MediaLibrary\Facades\MediaLibrary;
use RalphJSmit\Filament\MediaLibrary\FilamentMediaLibrary;
use Spatie\Image\Enums\Fit;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait HasContentMedia
{
    use InteractsWithMedia;

    public function getMediaLibraryCollectionName(): string
    {
        return 'library';
    }

    public function getMediaLibraryCollectionDisk(): ?string
    {
        return null;
    }

    public function getMediaLibraryCollectionConversionsDisk(): ?string
    {
        return null;
    }

    public function registerMediaCollections(): void
    {
        $mediaCollection = $this
            ->addMediaCollection($this->getMediaLibraryCollectionName())
            ->singleFile();

        if ($disk = $this->getMediaLibraryCollectionDisk()) {
            $mediaCollection->useDisk($disk);
        }

        if ($conversionsDisk = $this->getMediaLibraryCollectionConversionsDisk()) {
            $mediaCollection->storeConversionsOnDisk($conversionsDisk);
        }
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        foreach (MediaLibrary::getRegisterMediaConversionsUsing() as $registerMediaConversions) {
            $registerMediaConversions($this, $media);
        }

        // Responsive
        if (FilamentMediaLibrary::get()->isConversionResponsiveEnabled()) {
            $conversion = $this
                ->addMediaConversion('responsive')
                ->withResponsiveImages();

            if ($callback = FilamentMediaLibrary::get()->getConversionResponsiveCallback()) {
                $callback($conversion);
            }
        }

        // 800
        if (FilamentMediaLibrary::get()->isConversionMediumEnabled()) {
            $conversion = $this
                ->addMediaConversion('800')
                ->width(FilamentMediaLibrary::get()->getConversionMediumWidth());

            if ($callback = FilamentMediaLibrary::get()->getConversionMediumCallback()) {
                $callback($conversion);
            }
        }

        // 400
        if (FilamentMediaLibrary::get()->isConversionSmallEnabled()) {
            $conversion = $this
                ->addMediaConversion('400')
                ->width(FilamentMediaLibrary::get()->getConversionSmallWidth());

            if ($callback = FilamentMediaLibrary::get()->getConversionSmallCallback()) {
                $callback($conversion);
            }
        }

        if (FilamentMediaLibrary::get()->isConversionThumbEnabled()) {
            $usesSpatieMedialibraryV11 = class_exists('Spatie\Image\Enums\Fit');

            $conversion = $this
                ->addMediaConversion('thumb')
                ->fit(
                    $usesSpatieMedialibraryV11 ? Fit::Crop : Manipulations::FIT_CROP,
                    FilamentMediaLibrary::get()->getConversionThumbWidth(),
                    FilamentMediaLibrary::get()->getConversionThumbHeight(),
                )
                ->nonQueued();

            if ($callback = FilamentMediaLibrary::get()->getConversionThumbCallback()) {
                $callback($conversion);
            }
        }
    }
}
