<?php

namespace RalphJSmit\Filament\MediaLibrary;

use RalphJSmit\Filament\MediaLibrary\FilamentMediaLibrary\HasAcceptedFileTypesConfiguration;
use RalphJSmit\Filament\MediaLibrary\FilamentMediaLibrary\HasConversionsConfiguration;
use RalphJSmit\Filament\MediaLibrary\FilamentMediaLibrary\HasDiskConfiguration;
use RalphJSmit\Filament\MediaLibrary\FilamentMediaLibrary\HasModalsConfiguration;
use RalphJSmit\Filament\MediaLibrary\FilamentMediaLibrary\HasModelConfiguration;
use RalphJSmit\Filament\MediaLibrary\FilamentMediaLibrary\HasNavigationConfiguration;
use RalphJSmit\Filament\MediaLibrary\FilamentMediaLibrary\HasPreviewConversionsConfiguration;
use RalphJSmit\Filament\MediaLibrary\FilamentMediaLibrary\HasRegisterConfiguration;
use RalphJSmit\Filament\MediaLibrary\FilamentMediaLibrary\HasSettingsConfiguration;
use RalphJSmit\Filament\MediaLibrary\FilamentMediaLibrary\InitializesPluginFromConfiguration;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Facades\FilamentView;
use Illuminate\Database\Eloquent\Relations\Relation;
use Livewire\Livewire;

class FilamentMediaLibrary implements Plugin
{
    use HasAcceptedFileTypesConfiguration;
    use HasConversionsConfiguration;
    use HasDiskConfiguration;
    use HasModalsConfiguration;
    use HasModelConfiguration;
    use HasNavigationConfiguration;
    use HasPreviewConversionsConfiguration;
    use HasRegisterConfiguration;
    use HasSettingsConfiguration;
    use InitializesPluginFromConfiguration;

    public const RENDER_HOOK_MEDIA_INFO_PREVIEW_BEFORE = 'media-library::media-info.preview.before';

    public const RENDER_HOOK_MEDIA_INFO_PREVIEW_AFTER = 'media-library::media-info.preview.after';

    public const RENDER_HOOK_MEDIA_INFO_TITLE_BEFORE = 'media-library::media-info.title.before';

    public const RENDER_HOOK_MEDIA_INFO_TITLE_AFTER = 'media-library::media-info.title.after';

    public const RENDER_HOOK_MEDIA_INFO_ACTIONS_BEFORE = 'media-library::media-info.actions.before';

    public const RENDER_HOOK_MEDIA_INFO_ACTIONS_AFTER = 'media-library::media-info.actions.after';

    public static function make(): static
    {
        $plugin = app(static::class);

        $plugin->setUp();

        return $plugin;
    }

    public static function get(): static
    {
        if (! ($currentPanel = filament()->getCurrentOrDefaultPanel())) {
            return app(static::class);
        }

        /** @var static */
        return $currentPanel->getPlugin(app(static::class)->getId());
    }

    public function getId(): string
    {
        return 'ralphjsmit/laravel-filament-media-library';
    }

    public function register(Panel $panel): void
    {
        Livewire::component('media-library::media.upload-media', $this->getUploadMediaComponent());
        Livewire::component('media-library::media.media-info', $this->getMediaInfoComponent());
        Livewire::component('media-library::media.browse-library', $this->getBrowseLibraryComponent());

        $panel->pages($this->getRegistrablePages());
    }

    public function boot(Panel $panel): void
    {
        FilamentView::registerRenderHook('panels::page.start', function (): string {
            return view('media-library::forms.components.media-picker.modal')->render();
        });

        FilamentView::registerRenderHook('panels::simple-page.start', function () use ($panel): ?string {
            if ($panel->getLoginUrl() && auth()->guest()) {
                // Do not include modal on login page for guests...
                return null;
            }

            return view('media-library::forms.components.media-picker.modal')->render();
        });

        Relation::morphMap([
            'filament_media_library_item' => FilamentMediaLibrary::get()->getModelItem(),
        ], true);
    }

    protected function setUp(): void
    {
        $this->initializePluginFromConfigurationIfPresent();
    }
}
