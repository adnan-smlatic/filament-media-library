<?php

namespace RalphJSmit\Filament\MediaLibrary\Facades;

use Closure;
use Illuminate\Support\Facades\Facade;
use RalphJSmit\Filament\MediaLibrary\Support\MediaLibraryManager;

/**
 * @method static void registerMediaConversions(Closure $callback, bool $merge = true)
 * @method static Closure[] getRegisterMediaConversionsUsing()
 * @method static void registerMediaInfoInformationUsing(Closure $callback, bool $merge = true)
 * @method static Closure[] getRegisterMediaInfoInformationUsing()
 * @method static void registerMediaInfoFormFields(Closure $callback, bool $merge = true)
 * @method static Closure[] getRegisterMediaInfoFormFieldsUsing()
 */
class MediaLibrary extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return MediaLibraryManager::class;
    }
}
