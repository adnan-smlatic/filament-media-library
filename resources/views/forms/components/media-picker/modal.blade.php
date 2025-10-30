@php
    use Filament\Facades\Filament;
    use RalphJSmit\Filament\MediaLibrary\FilamentMediaLibrary;
@endphp

@once
    <div class="relative z-[100] h-0" x-data="{
        statePath: null,
        folder: null,
    }">
        <x-filament::modal
            :width="FilamentMediaLibrary::get()->getMediaPickerModalWidth()"
            id="media-library-picker"
            x-on:open-modal.window="
                if ($event.detail.id !== 'media-library-picker') {
                    return;
                }

                statePath = $event.detail.getStatePath;
                $store.browseLibrary.isMultiple = $event.detail.isMultiple;

                $store.browseLibrary.selectMediaItem($event.detail.currentSelectedMediaItemIds)
                $dispatch('browse-library-load', { folder: $event.detail.folder, defaultFolder: $event.detail.defaultFolder })

                if (! $store.browseLibrary.isMultiple) {
                    $dispatch('media-item-selected', { ids: $store.browseLibrary.selectedMediaItemId, folder: $event.detail.folder })
                }

                folder = $event.detail.folder

                open()
            "
            x-init="$watch('$store.browseLibrary.latestSelectedMediaItemId', (value) => {
                $dispatch('media-item-selected', { ids: value, folder: folder });
            })"
        >
            <div class="relative h-full max-h-[80vh]">
                <div class="flex h-full flex-row space-x-2 rtl:space-x-reverse">
                    <div class="flex-grow md:me-8">
                        <div class="mt-2 flex items-center justify-between">
                            <h2 class="text-xl font-bold tracking-tight">
                                {{ \Illuminate\Support\Str::ucfirst(__('filament-media-library::translations.components.media-picker.title')) }}
                            </h2>
                            @php
                                if (! \Illuminate\Support\Facades\Gate::getPolicyFor(FilamentMediaLibrary::get()->getModelItem()) || ! Filament::auth()) {
                                    $canCreate = true;
                                } else {
                                    $canCreate = Filament::auth()->user()?->can('create', FilamentMediaLibrary::get()->getModelItem());
                                }
                            @endphp

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
                            class="mt-4 h-[calc(100%_-_60px)] max-h-full overflow-y-scroll rounded-lg bg-gray-100 px-6 py-2 dark:bg-gray-950"
                        >
                            @livewire(FilamentMediaLibrary::get()->getBrowseLibraryComponent(), [
                                'defer' => true,
                            ])
                        </div>
                    </div>

                    <aside
                        class="sticky end-0 top-0 ms-auto hidden h-full w-full min-w-[280px] max-w-[320px] flex-grow-0 self-start overflow-y-scroll px-2 py-8 md:block"
                        @unless (FilamentMediaLibrary::get()->shouldShowMediaInfoOnMultipleSelection())
                            x-show="! $store.browseLibrary.isMultiple"
                        @endunless
                    >
                        @unless (Livewire::current() instanceof \RalphJSmit\Filament\MediaLibrary\Media\Pages\MediaLibrary
                                 || Livewire::current() instanceof \RalphJSmit\Filament\MediaLibrary\Media\Components\MediaInfo
                                 || Livewire::current() instanceof \RalphJSmit\Filament\MediaLibrary\Media\Components\BrowseLibrary)
                            @livewire(FilamentMediaLibrary::get()->getMediaInfoComponent())
                        @endunless
                    </aside>
                </div>
            </div>

            <x-slot name="footer">
                <div
                    @class([
                        'flex space-x-2 rtl:space-x-reverse',
                        'justify-start' => config('filament.layout.forms.actions.alignment') === 'left',
                        'justify-center' => config('filament.layout.forms.actions.alignment') === 'center',
                        'justify-end' => config('filament.layout.forms.actions.alignment') === 'right',
                    ])
                >
                    <x-filament::button
                        outlined
                        color="gray"
                        {{-- Do not include statePath as parameter in the x-on:click to close-modal, otherwise the media picker will update the selected values --}}
                        x-on:click="$dispatch('close-modal', {id: 'media-library-picker'})"
                    >
                        {{ \Illuminate\Support\Str::ucfirst(__('filament-media-library::translations.phrases.cancel')) }}
                    </x-filament::button>

                    <x-filament::button
                        x-on:click="$dispatch('close-modal', {id: 'media-library-picker', statePath: statePath})"
                    >
                        {{ \Illuminate\Support\Str::ucfirst(__('filament-media-library::translations.phrases.update-and-close')) }}
                    </x-filament::button>
                </div>
            </x-slot>
        </x-filament::modal>
    </div>
@endonce
