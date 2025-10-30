@php
    use Filament\Facades\Filament;
    use RalphJSmit\Filament\MediaLibrary\FilamentMediaLibrary;
@endphp

<div
    x-data="{
        state: $wire.entangle('{{ $statePath = $getStatePath() }}'),
    }"
    wire:key="media-picker-{{ $statePath = $getStatePath() }}"
    x-init="
        $nextTick(function () {
            $store.browseLibrary.isMultiple = @Js($isMultiple)

            $store.browseLibrary.selectMediaItem(@Js($state ?? []))

            console.log('dispatched event')
            $dispatch('browse-library-load', {
                folder: @Js($folderKey),
                defaultFolder: @Js($defaultFolderKey),
            })

            $watch('$store.browseLibrary.latestSelectedMediaItemId', (value) => {
                // Inform media info:
                $dispatch('media-item-selected', { ids: value, folder: @Js($folderKey) })

                state = @Js($isMultiple)
                    ? $store.browseLibrary.selectedMediaItemIds
                    : $store.browseLibrary.selectedMediaItemId
            })
        })
    "
>
    <div class="h-full max-h-[80vh] overflow-y-scroll">
        <div class="flex h-full flex-row space-x-2 rtl:space-x-reverse">
            <div class="flex-grow md:me-8">
                <div class="mt-2 flex items-center justify-between">
                    <h2 class="text-xl font-bold tracking-tight">
                        {{ \Illuminate\Support\Str::ucfirst(__('filament-media-library::translations.components.media-picker.title')) }}
                    </h2>

                    @if ($canCreate && FilamentMediaLibrary::get()->shouldShowUploadBoxByDefault() === false)
                        <x-filament::button
                            tag="button"
                            x-data="{}"
                            x-on:click="$dispatch('toggle-upload-box')"
                            icon="heroicon-o-arrow-up-tray"
                        >
                            <strong>
                                {{ \Illuminate\Support\Str::ucfirst(__('filament-media-library::translations.phrases.upload')) }}
                            </strong>
                        </x-filament::button>
                    @endif
                </div>
                <div
                    class="-mb-[40px] mt-4 h-full max-h-full overflow-y-scroll rounded-lg bg-gray-100 px-6 py-2 dark:bg-gray-950"
                >
                    @livewire(FilamentMediaLibrary::get()->getBrowseLibraryComponent())
                </div>
            </div>

            <aside
                class="sticky end-0 top-0 ms-auto hidden h-full w-full min-w-[280px] max-w-[320px] flex-grow-0 self-start overflow-y-scroll px-2 py-8 md:block"
                @unless (FilamentMediaLibrary::get()->shouldShowMediaInfoOnMultipleSelection())
                    x-show="! $store.browseLibrary.isMultiple"
                @endunless
            >
                @livewire(FilamentMediaLibrary::get()->getMediaInfoComponent())
            </aside>
        </div>
    </div>
</div>
