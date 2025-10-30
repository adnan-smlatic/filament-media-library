<?php

namespace RalphJSmit\Filament\MediaLibrary\Media\Pages;

use Filament\Actions\Action;
use Filament\Panel;
use Filament\Facades\Filament;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use RalphJSmit\Filament\MediaLibrary\FilamentMediaLibrary;
use RalphJSmit\Filament\MediaLibrary\Media\Components\Concerns\CanOpenMediaLibraryFolder;
use RalphJSmit\Filament\MediaLibrary\Support\Livewire\CanBeRefreshed;

class MediaLibrary extends Page implements HasForms
{
    use CanBeRefreshed;
    use CanOpenMediaLibraryFolder;
    use InteractsWithForms;
    use WithPagination;

    protected static string | \UnitEnum | null $navigationGroup = 'Media';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-photo';

    protected static ?int $navigationSort = 0;

    protected string $view = 'media-library::pages.media-library';

    public function displayUploadBox(): void
    {
        $this->dispatch('toggle-upload-box');
    }

    protected function getActions(): array
    {
        return [
            Action::make('upload')
                ->label(Str::ucfirst(__('filament-media-library::translations.phrases.upload')))
                ->action('displayUploadBox')
                ->icon('heroicon-o-arrow-up-tray')
                ->visible(function () {
                    if (FilamentMediaLibrary::get()->shouldShowUploadBoxByDefault()) {
                        return false;
                    }

                    if (! Gate::getPolicyFor(FilamentMediaLibrary::get()->getModelItem())) {
                        return true;
                    }

                    return Filament::auth()->user()?->can('create', [FilamentMediaLibrary::get()->getModelItem(), $this->mediaLibraryFolder]);
                }),
        ];
    }

    public function getTitle(): string | Htmlable
    {
        return FilamentMediaLibrary::get()->getPageTitle() ?? parent::getTitle();
    }

    public static function getNavigationGroup(): ?string
    {
        return FilamentMediaLibrary::get()->getNavigationGroup() ?? parent::getNavigationGroup();
    }

    public static function getNavigationSort(): ?int
    {
        return FilamentMediaLibrary::get()->getNavigationSort() ?? parent::getNavigationSort();
    }

    public static function getNavigationIcon(): ?string
    {
        return FilamentMediaLibrary::get()->getNavigationIcon() ?? parent::getNavigationIcon();
    }

    public static function getActiveNavigationIcon(): ?string
    {
        return FilamentMediaLibrary::get()->getActiveNavigationIcon() ?? parent::getActiveNavigationIcon();
    }

    public static function getNavigationLabel(): string
    {
        return FilamentMediaLibrary::get()->getNavigationLabel() ?? parent::getNavigationLabel();
    }

    public static function getSlug(?Panel $panel = null): string
    {
        if (filament()->getCurrentOrDefaultPanel()?->hasPlugin(FilamentMediaLibrary::make()->getId())) {
            return FilamentMediaLibrary::get()->getSlug() ?? parent::getSlug();
        }

        return parent::getSlug();
    }
}
