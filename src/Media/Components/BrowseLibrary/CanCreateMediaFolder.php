<?php

namespace RalphJSmit\Filament\MediaLibrary\Media\Components\BrowseLibrary;

use Filament\Schemas\Schema;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use RalphJSmit\Filament\MediaLibrary\FilamentMediaLibrary;
use RalphJSmit\Filament\MediaLibrary\Media\Components\BrowseLibrary;

/**
 * @mixin BrowseLibrary
 */
trait CanCreateMediaFolder
{
    protected function getCreateMediaFolderForm(): Schema
    {
        return Schema::make($this)
            
            ->schema([
                TextInput::make('name')
                    ->hiddenLabel()
                    ->rules(['string', 'max:255'])
                    ->placeholder(Str::ucfirst(__('filament-media-library::translations.components.browse-library.modals.create-media-folder.form.name.placeholder')))
                    ->autofocus()
                    ->required()
                    ->lazy(),
            ]);
    }

    public function openCreateMediaFolderModal(): void
    {
        $this->createMediaFolderForm->fill();

        $this->dispatch('open-modal', id: 'create-media-folder');
    }

    public function closeCreateMediaFolderModal(): void
    {
        $this->dispatch('close-modal', id: 'create-media-folder');
    }

    public function createMediaFolder(): void
    {
        $state = $this->createMediaFolderForm->getState();

        $mediaLibraryFolder = FilamentMediaLibrary::get()->getModelFolder()::create([
            'parent_id' => $this->mediaLibraryFolder?->getKey(),
            'name' => $state['name'],
        ]);

        $this->openMediaLibraryFolder($mediaLibraryFolder->id);

        Notification::make()
            ->title(__('filament-media-library::translations.components.browse-library.modals.create-media-folder.messages.created.body'))
            ->success()
            ->send();

        $this->dispatch('$refresh')->to(FilamentMediaLibrary::get()->getMediaInfoComponent());

        $this->closeCreateMediaFolderModal();
    }

    public function canCreateFolder(): bool
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

        return Filament::auth()->user()->can('create', [FilamentMediaLibrary::get()->getModelFolder(), $this->mediaLibraryFolder]);
    }
}
