@php
    use RalphJSmit\Filament\MediaLibrary\FilamentMediaLibrary;
@endphp

<x-filament::page>
    <div class="flex flex-row">
        <div class="me-2 flex-grow">
            @livewire(FilamentMediaLibrary::get()->getBrowseLibraryComponent())
        </div>

        <aside
            @class([
                'sticky z-10 top-24 -me-8 hidden min-h-full w-full w-full min-w-[320px] flex-grow-0 self-start rounded-s-xl bg-white p-8 dark:bg-gray-900 md:ms-8 md:block md:max-w-[360px]',
                // Only round the right side if the content width is not full width.
                'rounded-e-xl' => ($this->getMaxContentWidth() ?? config('filament.layout.max_content_width') ?? '7xl') !== 'full',
            ])
        >
            @livewire(FilamentMediaLibrary::get()->getMediaInfoComponent())
        </aside>

        <div class="md:hidden" x-data x-ref="modalContainerMediaInfo">
            <x-filament::modal
                width="2xl"
                id="media-library-media-info"
                :header="__('filament-media-library::translations.components.media-info.heading')"
                close-button
                {{-- Only open the modal if the media item is hidden and not on md. Otherwise the modal will open, the parent container will be invisible, but Filament will still add overflow:hidden to the body. --}}
                {{-- Source for code: https://stackoverflow.com/a/33456469 --}}
                x-on:media-item-selected.window="
                    if (!!($refs.modalContainerMediaInfo.offsetWidth || $refs.modalContainerMediaInfo.offsetHeight || $refs.modalContainerMediaInfo.getClientRects().length)) {
                        $dispatch('open-modal', { id: 'media-library-media-info' })
                    }
                "
            >
                @livewire(FilamentMediaLibrary::get()->getMediaInfoComponent())
            </x-filament::modal>
        </div>
    </div>
</x-filament::page>
