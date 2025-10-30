<?php

namespace RalphJSmit\Filament\MediaLibrary\Media\Components\BrowseLibrary;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Checkbox;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Gate;
use RalphJSmit\Filament\MediaLibrary\FilamentMediaLibrary;
use RalphJSmit\Filament\MediaLibrary\Media\Components\BrowseLibrary;
use RalphJSmit\Filament\MediaLibrary\Media\Models\MediaLibraryFolder;

/**
 * @mixin BrowseLibrary
 */
trait CanDeleteMediaFolder
{
    public function mountCanDeleteMediaFolder(): void
    {
        $this->deleteMediaFolderForm->fill();
    }

    public function openDeleteMediaFolderModal(mixed $mediaLibraryFolderId): void
    {
        $mediaLibraryFolder = FilamentMediaLibrary::get()->getModelFolder()::findOrFail($mediaLibraryFolderId);

        $this->activeMediaLibraryFolder = $mediaLibraryFolder;

        $this->dispatch('open-modal', id: 'delete-media-folder');
    }

    public function closeDeleteMediaFolderModal(): void
    {
        $this->activeMediaLibraryFolder = null;

        $this->dispatch('close-modal', id: 'delete-media-folder');
    }

    public function deleteMediaFolder(): void
    {
        $state = $this->deleteMediaFolderForm->getState();

        $mediaLibraryFolder = $this->activeMediaLibraryFolder;

        $this->activeMediaLibraryFolder = null;

        if ($state['include_children']) {
            $mediaLibraryFolder->deleteRecursive();
        } else {
            $mediaLibraryFolder->delete();
        }

        $this->deleteMediaFolderForm->fill();

        Notification::make()
            ->title(__('filament-media-library::translations.components.browse-library.modals.delete-media-folder.messages.deleted.body'))
            ->success()
            ->send();

        $this->dispatch('$refresh')->to(FilamentMediaLibrary::get()->getMediaInfoComponent());

        $this->closeDeleteMediaFolderModal();
    }

    public function canDeleteFolder(MediaLibraryFolder $mediaLibraryFolder): bool
    {
        if (! Gate::getPolicyFor(FilamentMediaLibrary::get()->getModelFolder())) {
            return true;
        }

        if (! Filament::getCurrentOrDefaultPanel()) {
            return true;
        }

        if (Filament::auth()->guest()) {
            return true;
        }

        return Filament::auth()->user()->can('delete', $mediaLibraryFolder);
    }

    protected function getDeleteMediaFolderForm(): Schema
    {
        return Schema::make($this)
            
            ->schema([
                Checkbox::make('include_children')
                    ->label(__('filament-media-library::translations.components.browse-library.modals.delete-media-folder.form.fields.include_children.label'))
                    ->helperText(function (bool $state) {
                        if (! $state) {
                            return null;
                        }

                        return __('filament-media-library::translations.components.browse-library.modals.delete-media-folder.form.fields.include_children.helper_text');
                    })
                    ->live()
                    ->default(false),
            ]);
    }
}
