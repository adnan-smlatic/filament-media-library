<?php

namespace RalphJSmit\Filament\MediaLibrary;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Blade;
use Livewire\Livewire;
use RalphJSmit\Filament\MediaLibrary\Media\Components\BrowseLibrary;
use RalphJSmit\Filament\MediaLibrary\Media\Components\MediaInfo;
use RalphJSmit\Filament\MediaLibrary\Media\Components\UploadMedia;
use RalphJSmit\Filament\MediaLibrary\Media\Models\MediaLibraryItem;
use RalphJSmit\Filament\MediaLibrary\Support\MediaLibraryManager;
use RalphJSmit\Filament\MediaLibrary\Support\NamespaceManager;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentMediaLibraryServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('ralphjsmit/laravel-filament-media-library')
            ->hasConfigFile()
            ->hasViews('media-library')
            ->hasMigrations([
                'create_filament_media_library_table',
                'create_filament_media_library_folders_table',
            ])
            ->hasTranslations();

        $this->app->singleton(MediaLibraryManager::class);
    }

    public function packageBooted(): void
    {
        parent::packageBooted();

        $this->mergeConfigFrom(__DIR__ . '/../config/filament-media-library.php', 'filament-media-library');

        NamespaceManager::registerNamespace('RalphJSmit\\Filament\\MediaLibrary\\', __DIR__);

        // Register as default for people who use the MediaLibraryItem model outside of a panel.
        // If people are using a custom MediaLibraryItem model, the morph map is registered
        // by the plugin class in each panel. So this only acts as a default model.
        Relation::morphMap([
            'filament_media_library_item' => MediaLibraryItem::class,
        ], true);

        // Register directive globally, for use when no panel is configured.
        Blade::directive(
            'mediaPickerModal',
            fn (): View => view('media-library::forms.components.media-picker.modal')
        );

        Livewire::component('media-library::media.upload-media', UploadMedia::class);
        Livewire::component('media-library::media.media-info', MediaInfo::class);
        Livewire::component('media-library::media.browse-library', BrowseLibrary::class);
    }
}
