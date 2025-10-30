<?php

namespace RalphJSmit\Filament\MediaLibrary\Support\Livewire;

use Livewire\Attributes\On;

trait CanBeRefreshed
{
    /**
     * Livewire V3 refresh handler.
     */
    #[On('$refresh')]
    public function refreshInternal(): void
    {
        //
    }
}
