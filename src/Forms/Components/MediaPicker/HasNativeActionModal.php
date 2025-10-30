<?php

namespace RalphJSmit\Filament\MediaLibrary\Forms\Components\MediaPicker;

use Filament\Actions\Action;
use Filament\Forms\Components\Field;
use Closure;
use Filament\Facades\Filament;
use Filament\Forms;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use RalphJSmit\Filament\MediaLibrary\FilamentMediaLibrary;

trait HasNativeActionModal
{
    protected bool | Closure $isNativeActionModalUsed = false;

    /**
     * Temporary experimental feature to render the hint action modal
     * inside a Filament Action instead of a custom modal file in V3.
     *
     * This feature is only to be used for `MediaPicker`s outside
     * any panel, and is intended as temporary measure to fix
     * a certain Livewire `$slot` error until V4 refactors
     * all `MediaPicker`s and actions to native actions.
     */
    public function nativeActionModal(bool | Closure $condition = true): static
    {
        $this->isNativeActionModalUsed = $condition;

        return $this;
    }

    public function getNativeActionModalAction(): Action
    {
        return Action::make('open_media_library_picker')
            ->button()
            ->modalWidth('7xl')
            ->label(fn () => $this->getButtonLabel() ?? Str::ucfirst(trans_choice('filament-media-library::translations.media.choose-image', $this->isMultiple() ? 2 : 1)))
            ->modalHeading(Str::ucfirst(__('filament-media-library::translations.components.media-picker.title')))
            ->modalSubmitActionLabel(Str::ucfirst(__('filament-media-library::translations.phrases.update-and-close')))
            ->modalCancelActionLabel(Str::ucfirst(__('filament-media-library::translations.phrases.cancel')))
            ->schema(fn () => [
                Field::make('selected_media_item_ids')
                    ->view('media-library::forms.components.media-picker.modal-native-action')
                    ->viewData([
                        'state' => $this->getState(),
                        'isMultiple' => $this->isMultiple(),
                        'folderKey' => $this->getFolder()?->getKey(),
                        'defaultFolderKey' => $this->getDefaultFolder()?->getKey(),
                        'canCreate' => value(function () {
                            if (! Filament::getCurrentOrDefaultPanel() || ! Gate::getPolicyFor(FilamentMediaLibrary::get()->getModelItem())) {
                                return true;
                            }

                            return Filament::auth()->user()?->can('create', FilamentMediaLibrary::get()->getModelItem());
                        }),
                    ]),
            ])
            ->action(function (array $data) {
                $this->state($data['selected_media_item_ids']);
            });
    }

    public function isNativeActionModalUsed(): bool
    {
        return $this->evaluate($this->isNativeActionModalUsed);
    }
}
