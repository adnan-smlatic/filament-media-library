<?php

namespace RalphJSmit\Filament\MediaLibrary\Media\Components\Concerns;

use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use RalphJSmit\Filament\MediaLibrary\FilamentMediaLibrary;
use RalphJSmit\Filament\MediaLibrary\Media\Models\MediaLibraryFolder;

/**
 * @property-read MediaLibraryFolder $mediaLibraryFolder
 */
trait CanOpenMediaLibraryFolder
{
    /**
     * Allow opening a folder by URL when linking to MediaLibrary page using ?folder={ID},
     * plus keep the folder in the URL using a query string when reloading a page.
     */
    #[Url(as: 'folder')]
    public mixed $mediaLibraryFolderKey = null;

    public function bootCanOpenMediaLibraryFolder(): void
    {
        $this->listeners['openMediaLibraryFolder'] = 'openMediaLibraryFolder';
    }

    public function openMediaLibraryFolder(null | int | string $mediaLibraryFolderId): void
    {
        $this->mediaLibraryFolderKey = $mediaLibraryFolderId;

        unset($this->mediaLibraryFolder);
    }

    #[Computed]
    public function mediaLibraryFolder(): ?MediaLibraryFolder
    {
        if (! $this->mediaLibraryFolderKey) {
            return null;
        }

        return FilamentMediaLibrary::get()->getModelFolder()::find($this->mediaLibraryFolderKey);
    }
}
