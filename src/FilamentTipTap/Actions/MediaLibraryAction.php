<?php

namespace RalphJSmit\Filament\MediaLibrary\FilamentTipTap\Actions;

use Filament\Actions\Action;
use Filament\Schemas\Schema;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Database\Eloquent\Builder;
use RalphJSmit\Filament\MediaLibrary\FilamentMediaLibrary;
use RalphJSmit\Filament\MediaLibrary\Forms\Components\MediaPicker;
use RalphJSmit\Filament\MediaLibrary\Media\DataTransferObjects\MediaItemMeta;
use RalphJSmit\Filament\MediaLibrary\Media\Models\MediaLibraryItem;

class MediaLibraryAction extends Action
{
    public static function getDefaultName(): ?string
    {
        // Keep this name the same in order to be able to replace default media action.
        return 'filament_tiptap_media';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->mountUsing(function (TiptapEditor $component, Schema $schema, array $arguments) {
                $mediaLibraryItem = null;

                if ($mediaLibraryItemId = ($arguments['media'] ?? null)) {
                    $mediaLibraryItem = FilamentMediaLibrary::get()
                        ->getModelItem()::find($mediaLibraryItemId);
                }

                if ($arguments['title']) {
                    $mediaLibraryItem ??= FilamentMediaLibrary::get()
                        ->getModelItem()::query()
                        ->whereHas('media', function (Builder $query) use ($arguments) {
                            return $query->where('name', $arguments['title']);
                        })
                        ->first();
                }

                $schema->fill([
                    'media_library_item_id' => $mediaLibraryItem?->getKey(),
                ]);
            })
            ->modalHeading(function (TiptapEditor $component) {
                return __('filament-media-library::translations.filament-tip-tap.actions.media-library-action.modal-heading');
            })
            ->modalSubmitActionLabel(function (TiptapEditor $component) {
                return __('filament-media-library::translations.filament-tip-tap.actions.media-library-action.modal-submit-action-label');
            })
            ->modalWidth('md')
            ->form(function (TiptapEditor $component) {
                return [
                    MediaPicker::make('media_library_item_id')
                        ->required()
                        ->hiddenLabel()
                        ->acceptedFileTypes($component->getAcceptedFileTypes()),
                ];
            })
            ->action(function (TiptapEditor $component, array $data, self $action) {
                /** @var MediaLibraryItem $mediaLibraryItem */
                $mediaLibraryItem = FilamentMediaLibrary::get()->getModelItem()::find($data['media_library_item_id']);

                $mediaLibraryItemMeta = $action->getMediaLibraryItemMeta($mediaLibraryItem);

                $media = [
                    'src' => $mediaLibraryItemMeta->url,
                    'alt' => $mediaLibraryItemMeta->altText,
                    'title' => $mediaLibraryItemMeta->name,
                    'width' => $mediaLibraryItemMeta->width,
                    'height' => $mediaLibraryItemMeta->height,
                    'link_text' => null,
                    'media' => (string) $mediaLibraryItemMeta->id,
                ];

                if (FilamentMediaLibrary::get()->isConversionResponsiveEnabled()) {
                    $media['srcset'] = $mediaLibraryItem->getItem()->getSrcset('responsive');
                }

                $component->getLivewire()->dispatch(
                    'insertFromAction',
                    type: 'media',
                    statePath: $component->getStatePath(),
                    media: $media,
                );
            });
    }

    protected function getMediaLibraryItemMeta(MediaLibraryItem $mediaLibraryItem): MediaItemMeta
    {
        return $mediaLibraryItem->getMeta(true);
    }
}
