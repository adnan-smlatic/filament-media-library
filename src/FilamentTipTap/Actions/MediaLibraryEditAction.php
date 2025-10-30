<?php

namespace RalphJSmit\Filament\MediaLibrary\FilamentTipTap\Actions;

use Filament\Forms\Components\Actions\Action;

class MediaLibraryEditAction extends MediaLibraryAction
{
    public static function getDefaultName(): ?string
    {
        // Keep this name the same in order to be able to replace default media action.
        return 'filament_tiptap_edit_media';
    }
}
