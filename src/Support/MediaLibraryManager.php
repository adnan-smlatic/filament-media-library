<?php

namespace RalphJSmit\Filament\MediaLibrary\Support;

use Closure;

class MediaLibraryManager
{
    /** @var Closure[] */
    protected array $registerMediaConversionsUsing = [];

    /** @var Closure[] */
    protected array $registerMediaInfoInformationUsing = [];

    /** @var Closure[] */
    protected array $registerMediaInfoFormFieldsUsing = [];

    public function registerMediaConversions(Closure $callback, bool $merge = true): void
    {
        if (! $merge) {
            $this->registerMediaConversionsUsing = [$callback];
        } else {
            $this->registerMediaConversionsUsing[] = $callback;
        }
    }

    /** @return Closure[] */
    public function getRegisterMediaConversionsUsing(): array
    {
        return $this->registerMediaConversionsUsing;
    }

    public function registerMediaInfoInformationUsing(Closure $callback, bool $merge = true): void
    {
        if (! $merge) {
            $this->registerMediaInfoInformationUsing = [$callback];
        } else {
            $this->registerMediaInfoInformationUsing[] = $callback;
        }
    }

    /** @return Closure[] */
    public function getRegisterMediaInfoInformationUsing(): array
    {
        return $this->registerMediaInfoInformationUsing;
    }

    public function registerMediaInfoFormFields(Closure $callback, bool $merge = true): void
    {
        if (! $merge) {
            $this->registerMediaInfoFormFieldsUsing = [$callback];
        } else {
            $this->registerMediaInfoFormFieldsUsing[] = $callback;
        }
    }

    /** @return Closure[] */
    public function getRegisterMediaInfoFormFieldsUsing(): array
    {
        return $this->registerMediaInfoFormFieldsUsing;
    }
}
