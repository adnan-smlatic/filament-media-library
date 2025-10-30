<?php

namespace RalphJSmit\Filament\MediaLibrary\UrlGenerator;

use Illuminate\Support\Str;
use RalphJSmit\Filament\MediaLibrary\FilamentMediaLibrary;
use Spatie\MediaLibrary\Support\UrlGenerator\DefaultUrlGenerator;

class MediaLibraryUrlGenerator extends DefaultUrlGenerator
{
    public function getUrl(): string
    {
        if (FilamentMediaLibrary::get()->getDiskVisibility() === 'private') {
            return $this->getDisk()->temporaryUrl($this->getPathRelativeToRoot(), now()->addMinutes(30));
        }

        return parent::getUrl();
    }

    public function getBaseMediaDirectoryUrl(): string
    {
        if (FilamentMediaLibrary::get()->getDiskVisibility() === 'private') {
            return $this->getDisk()->temporaryUrl('/', now()->addMinutes(30));
        }

        return parent::getBaseMediaDirectoryUrl();
    }

    public function getResponsiveImagesDirectoryUrl(): string
    {
        $path = $this->pathGenerator->getPathForResponsiveImages($this->media);

        if (FilamentMediaLibrary::get()->getDiskVisibility() === 'private') {
            return Str::finish($this->getDisk()->temporaryUrl($path, now()->addMinutes(30)), '/');
        }

        return parent::getResponsiveImagesDirectoryUrl();
    }
}
