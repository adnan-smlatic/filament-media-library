@php
    use Filament\Support\Enums\Alignment;
    use RalphJSmit\Filament\MediaLibrary\FilamentMediaLibrary;
    use RalphJSmit\Filament\MediaLibrary\Media\DataTransferObjects\BrowseLibraryItem;
    use RalphJSmit\Filament\MediaLibrary\Media\Models\MediaLibraryItem;
    use Spatie\MediaLibrary\MediaCollections\Models\Media;
@endphp

<div>
    <div
        x-data="{
        {{-- The reset-selected-media-item event is dispatched from Livewire, so we need an event listener in addition to the global store. --}}
        @unless (FilamentMediaLibrary::get()->shouldShowUploadBoxByDefault())
            showUploadBox: false,
        @endunless
        }"
        @unless (FilamentMediaLibrary::get()->shouldShowUploadBoxByDefault())
            x-on:toggle-upload-box.window="showUploadBox = ! showUploadBox"
        @endunless
        {{-- This event is dispatched from Livewire, so we need an event listener in addition to the global store. --}}
        x-on:reset-selected-media-item.window="$store.browseLibrary.resetMediaItemsSelection()"
        x-on:browse-library-load.window="@this.call('loadMedia', $event.detail.folder, $event.detail.defaultFolder)"
    >
        @if ($this->canCreate())
            <div
                class="my-4 overflow-y-hidden"
                @unless (FilamentMediaLibrary::get()->shouldShowUploadBoxByDefault())
                    x-show="showUploadBox"
                    x-collapse
                    x-cloak
                @endunless
            >
                @livewire(FilamentMediaLibrary::get()->getUploadMediaComponent())
            </div>
        @endif

        <div class="mt-2 flex flex-col justify-between gap-4 lg:flex-row lg:items-end">
            <div class="flex flex-row items-center gap-x-4">
                @if ($this->canCreateFolder())
                    <button
                        wire:click="openCreateMediaFolderModal"
                        class="group flex h-8 flex-row items-center justify-center gap-x-0 overflow-hidden rounded-full bg-primary-500 pe-1 ps-1 transition-all hover:gap-x-1 hover:bg-primary-600 hover:pe-2 focus:gap-x-1 focus:bg-primary-600 focus:pe-2"
                    >
                        @svg('heroicon-o-plus', 'h-6 w-6 text-white')
                        <p
                            class="w-0 whitespace-nowrap text-white opacity-0 transition-[width] duration-500 ease-in group-hover:w-auto group-hover:opacity-100 group-focus:w-auto group-focus:opacity-100"
                        >
                            {{ \Illuminate\Support\Str::ucfirst(__('filament-media-library::translations.phrases.create-folder')) }}
                        </p>
                    </button>
                @endif

                <x-media-library::breadcrumbs :breadcrumbs="$this->breadcrumbs" />
            </div>
            <div class="flex flex-row items-center gap-x-2">
                <div
                    class="hidden flex-col items-center gap-y-1 text-sm leading-4 xl:flex"
                    x-show="$store.browseLibrary.isMultiple"
                >
                    <button
                        class="hover:text-primary-500"
                        x-data
                        x-on:click="$store.browseLibrary.selectMediaItems(@js($mediaLibraryItemKeys = $browseLibraryItems->filter(fn (BrowseLibraryItem $browseLibraryItem) => $browseLibraryItem->isMediaLibraryItem())->map(fn (BrowseLibraryItem $browseLibraryItem) => $browseLibraryItem->item->getKey())->values()))"
                        x-show="
                            $store.browseLibrary.selectedMediaItemIds.length == 0 ||
                                @js($mediaLibraryItemKeys->count()) != $store.browseLibrary.selectedMediaItemIds.length
                        "
                    >
                        {{ \Illuminate\Support\Str::ucfirst(__('filament-media-library::translations.phrases.select-all')) }}
                    </button>
                </div>
                <form wire:submit.prevent="searchForm">
                    <div class="flex">
                        <div class="">
                            {{ $this->searchForm }}
                        </div>
                        <div class="ps-2">
                            <x-filament::button color="gray" tag="button" type="submit" class="h-full">
                                <span wire:loading.remove wire:target="searchForm">
                                    {{ \Illuminate\Support\Str::ucfirst(__('filament-media-library::translations.phrases.search')) }}
                                </span>
                                <span wire:loading wire:target="searchForm">
                                    <x-media-library::spinner class="mt-1" />
                                </span>
                            </x-filament::button>
                        </div>
                    </div>
                </form>
                <form wire:submit.prevent="">
                    {{ $this->sortOrderForm }}
                </form>
            </div>
        </div>

        <div class="mt-8 flex w-full flex-row gap-x-8">
            @if ($browseLibraryItems?->isEmpty())
                <div
                    wire:loading.flex.remove
                    wire:target="loadMedia,openMediaLibraryFolder"
                    class="mt-[10vh] min-h-[40vh] w-full items-center justify-center"
                >
                    @include('media-library::media.empty-state')
                </div>
                <div
                    wire:loading.flex
                    wire:target="openMediaLibraryFolder"
                    class="mt-8 min-h-[60vh] w-full justify-center"
                >
                    <div
                        {{-- Starting at the `xl` breakpoint, we will use `auto-fill` instead of `auto-fit`. See https://css-tricks.com/auto-sizing-columns-css-grid-auto-fill-vs-auto-fit for reasoning. --}}
                        class="grid w-full grid-cols-2 gap-x-4 gap-y-4 sm:grid-cols-3 sm:gap-x-6 md:grid-cols-[repeat(auto-fit,_minmax(120px,_0.4fr))] xl:grid-cols-[repeat(auto-fill,_minmax(120px,_0.25fr))] xl:gap-x-8"
                    >
                        @for ($i = 0; $i < 22; $i++)
                            <div class="aspect-square animate-pulse rounded-lg bg-gray-200 dark:bg-gray-900"></div>
                        @endfor
                    </div>
                </div>
            @else
                <section class="min-h-[60vh] w-full">
                    <div class="mb-4 inline-flex" x-show="$store.browseLibrary.selectedMediaItemIds.length > 0">
                        <span class="rounded-s-full bg-gray-200 py-1 pe-1 ps-2.5 text-sm dark:bg-gray-800">
                            <span x-text="$store.browseLibrary.selectedMediaItemIds.length + ' '"></span>
                            <span x-show="$store.browseLibrary.selectedMediaItemIds.length === 1">
                                {{ __('filament-media-library::translations.phrases.selected-item-suffix') }}
                            </span>
                            <span x-show="$store.browseLibrary.selectedMediaItemIds.length !== 1">
                                {{ __('filament-media-library::translations.phrases.selected-items-suffix-plural') }}
                            </span>
                        </span>
                        <button
                            class="inline-flex aspect-square flex-row items-center justify-center rounded-e-full bg-gray-200 py-1 pe-1.5 ps-1 hover:bg-gray-300 dark:bg-gray-800 hover:dark:bg-gray-700"
                            x-on:click="$store.browseLibrary.resetMediaItemsSelection()"
                        >
                            <x-filament::icon icon="heroicon-o-x-mark" class="h-4 w-4" />
                        </button>
                    </div>
                    <div
                        {{-- Starting at the `xl` breakpoint, we will use `auto-fill` instead of `auto-fit`. See https://css-tricks.com/auto-sizing-columns-css-grid-auto-fill-vs-auto-fit for reasoning. --}}
                        class="grid min-h-0 w-full grid-cols-2 gap-x-4 gap-y-4 sm:grid-cols-3 sm:gap-x-6 md:grid-cols-[repeat(auto-fit,_minmax(120px,_0.4fr))] xl:grid-cols-[repeat(auto-fill,_minmax(120px,_0.25fr))] xl:gap-x-8"
                    >
                        @foreach ($browseLibraryItems ?? [] as $browseLibraryItem)
                            @if ($browseLibraryItem->isMediaLibraryItem())
                                @php
                                    /** @var MediaLibraryItem $mediaItem */
                                    $mediaItem = $browseLibraryItem->item;
                                    /** @var Media $image */
                                    $image = $mediaItem->getFirstMedia('library');

                                    $conversion = FilamentMediaLibrary::get()->getThumbnailMediaConversion();
                                @endphp

                                <label
                                    for="selectedMediaItemId-{{ $mediaItem->getKey() }}"
                                    class="relative cursor-pointer"
                                >
                                    <input
                                        type="checkbox"
                                        value="{{ $mediaItem->getKey() }}"
                                        @if ($this->canView($mediaItem))
                                            x-on:change="$store.browseLibrary.toggleMediaItemSelection($el.getAttribute('value'))"
                                        @endif
                                        id="selectedMediaItemId-{{ $mediaItem->getKey() }}"
                                        class="hidden"
                                    />
                                    <img
                                        src="{{ $image->hasGeneratedConversion($conversion) ? $image->getUrl($conversion) : $image->getFullUrl() }}"
                                        @if ($image->hasResponsiveImages($conversion))
                                            srcset="{{ $image->getSrcset($conversion) }}"
                                            {{-- Up to 1279px (Tailwind CSS xl) use 30vw, then 20vw. --}}
                                            sizes="(max-width: 1279px) 80vw, (min-width: 1279px) 50vw"
                                        @endif
                                        alt="{{ $mediaItem?->getMeta()->name }}"
                                        :class="{
                                            'ring-2 ring-offset-4 ring-primary-600 dark:ring-offset-gray-800': $store.browseLibrary.isMediaItemSelected('{{ $mediaItem->getKey() }}'),
                                            'object-cover rounded-lg dark:opacity-80': true,
                                            'focus-within:ring-2 focus-within:ring-offset-4 focus-within:ring-offset-gray-100 focus-within:ring-primary-600': true
                                        }"
                                    />
                                    <p
                                        class="mt-2 block truncate text-sm font-medium text-gray-900 dark:text-gray-100"
                                        x-tooltip.raw="{{ $image->name }}"
                                    >
                                        {{ $image->name }}
                                    </p>
                                    <p class="block text-sm font-medium text-gray-500">
                                        {{ $image->human_readable_size }}
                                    </p>
                                </label>
                            @elseif ($browseLibraryItem->isMediaLibraryFolder())
                                <div
                                    class="cursor-pointer"
                                    @if ($this->canView($browseLibraryItem->item))
                                        wire:click="openMediaLibraryFolder('{{ $browseLibraryItem->item->getKey() }}')"
                                    @endif
                                >
                                    <div
                                        class="relative aspect-square w-full rounded-lg bg-white/50 focus-within:ring-2 focus-within:ring-primary-600 focus-within:ring-offset-4 focus-within:ring-offset-gray-100 dark:bg-gray-800"
                                    >
                                        @svg('heroicon-s-folder', 'absolute start-4 top-4 w-10 text-primary-500 dark:opacity-80')
                                        <div class="me-2 ms-auto w-10 pt-4" x-on:click.stop="">
                                            <x-filament::dropdown placement="bottom-end">
                                                <x-slot name="trigger">
                                                    @if ($this->canRenameFolder($browseLibraryItem->item) || $this->canMoveFolder($browseLibraryItem->item) || $this->canDeleteFolder($browseLibraryItem->item))
                                                        <div
                                                            class="rounded-full p-2 hover:bg-gray-100 dark:hover:bg-gray-700"
                                                        >
                                                            @svg('heroicon-o-ellipsis-vertical', 'w-6 stroke-gray-400 dark:stroke-gray-600')
                                                        </div>
                                                    @endif
                                                </x-slot>

                                                <x-filament::dropdown.list>
                                                    @if ($this->canRenameFolder($browseLibraryItem->item))
                                                        <x-filament::dropdown.list.item
                                                            icon="heroicon-o-pencil"
                                                            color="gray"
                                                            wire:click="openRenameMediaFolderModal('{{ $browseLibraryItem->item->getKey() }}')"
                                                        >
                                                            {{ \Illuminate\Support\Str::ucfirst(__('filament-media-library::translations.phrases.rename-folder')) }}
                                                        </x-filament::dropdown.list.item>
                                                    @endif

                                                    @if ($this->canMoveFolder($browseLibraryItem->item))
                                                        <x-filament::dropdown.list.item
                                                            icon="heroicon-o-folder"
                                                            color="gray"
                                                            wire:click="openMoveMediaFolderModal('{{ $browseLibraryItem->item->getKey() }}')"
                                                        >
                                                            {{ \Illuminate\Support\Str::ucfirst(__('filament-media-library::translations.phrases.move-folder')) }}
                                                        </x-filament::dropdown.list.item>
                                                    @endif

                                                    @if ($this->canDeleteFolder($browseLibraryItem->item))
                                                        <x-filament::dropdown.list.item
                                                            icon="heroicon-o-trash"
                                                            color="danger"
                                                            wire:click="openDeleteMediaFolderModal('{{ $browseLibraryItem->item->getKey() }}')"
                                                        >
                                                            {{ \Illuminate\Support\Str::ucfirst(__('filament-media-library::translations.phrases.delete-folder')) }}
                                                        </x-filament::dropdown.list.item>
                                                    @endif
                                                </x-filament::dropdown.list>
                                            </x-filament::dropdown>
                                        </div>

                                        <div
                                            class="absolute bottom-0 start-0 w-full overflow-hidden truncate rounded-b-lg bg-gray-200 px-2 py-1.5 dark:bg-gray-900/75 dark:opacity-80"
                                        >
                                            {{ $browseLibraryItem->item->name }}
                                        </div>
                                    </div>
                                    <p
                                        class="mt-2 block truncate text-sm font-medium text-gray-900 dark:text-gray-100"
                                        x-tooltip.raw="{{ $browseLibraryItem->item->name }}"
                                    >
                                        {{ $browseLibraryItem->item->name }}
                                    </p>
                                    <p class="block text-sm font-medium text-gray-500">
                                        {{ trans_choice('filament-media-library::translations.sentences.folder-files', $browseLibraryItem->getChildrenCount(), ['count' => $browseLibraryItem->getChildrenCount()]) }}
                                    </p>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    @php
                        /** @var Paginator $browseLibraryItems */
                        $tableRecordsPerPageSelectOptions = $this->getTableRecordsPerPageSelectOptions();
                    @endphp

                    @if ($browseLibraryItems?->hasPages() || $browseLibraryItems->count() > $tableRecordsPerPageSelectOptions[0])
                        <div class="mt-8 w-full rounded-xl bg-white px-3 py-3 dark:bg-gray-700">
                            <x-filament::pagination
                                :paginator="$browseLibraryItems"
                                :page-options="$tableRecordsPerPageSelectOptions"
                            />
                        </div>
                    @endif
                </section>
            @endif
        </div>

        <x-filament::modal
            id="create-media-folder"
            :alignment="Alignment::Center"
            :footerActionsAlignment="Alignment::End"
            :heading="__('filament-media-library::translations.components.browse-library.modals.create-media-folder.heading')"
            :description="__('filament-media-library::translations.components.browse-library.modals.create-media-folder.subheading')"
            x-on:close-modal.window="if ($event.detail.id === 'create-media-folder') { $nextTick(() => document.getElementsByTagName('html')[0].style.overflow = null) }"
        >
            {{--
                For some reason, the Alpine.js `x-trap.noscroll` does not remove the scroll when closing the modal in this case.
                In the (revamped) v4 of this package I will refactor this to use Filament's native actions, which should fix
                this issue. Therefore, this temporary fix is for now the best way to handle this and restore the scrolling.
            --}}
            <form wire:submit.prevent="createMediaFolder">{{ $this->createMediaFolderForm }}</form>
            <x-slot name="footer">
                <div
                    @class([
                        'flex space-x-2 rtl:space-x-reverse',
                        //                        'justify-start' =>  config('filament.layout.forms.actions.alignment') === 'left',
                        //                        'justify-center' => config('filament.layout.forms.actions.alignment') === 'center',
                        'justify-end' => true || config('filament.layout.forms.actions.alignment') === 'right',
                    ])
                >
                    <x-filament::button color="gray" wire:click="closeCreateMediaFolderModal">
                        {{ \Illuminate\Support\Str::ucfirst(__('filament-media-library::translations.phrases.cancel')) }}
                    </x-filament::button>
                    <x-filament::button color="primary" wire:click="createMediaFolder">
                        {{ \Illuminate\Support\Str::ucfirst(__('filament-media-library::translations.phrases.create')) }}
                    </x-filament::button>
                </div>
            </x-slot>
        </x-filament::modal>
        <x-filament::modal
            id="rename-media-folder"
            :alignment="Alignment::Center"
            :footer-actions-alignment="Alignment::End"
            :heading="__('filament-media-library::translations.components.browse-library.modals.rename-media-folder.heading')"
            x-on:close-modal.window="if ($event.detail.id === 'rename-media-folder') { $nextTick(() => document.getElementsByTagName('html')[0].style.overflow = null) }"
        >
            {{--
                For some reason, the Alpine.js `x-trap.noscroll` does not remove the scroll when closing the modal in this case.
                In the (revamped) v4 of this package I will refactor this to use Filament's native actions, which should fix
                this issue. Therefore, this temporary fix is for now the best way to handle this and restore the scrolling.
            --}}
            <form wire:submit.prevent="renameMediaFolder">
                {{ $this->renameMediaFolderForm }}
            </form>

            <x-slot name="footer">
                <div
                    @class([
                        'flex space-x-2 rtl:space-x-reverse',
                        //                        'justify-start' =>  config('filament.layout.forms.actions.alignment') === 'left',
                        //                        'justify-center' => config('filament.layout.forms.actions.alignment') === 'center',
                        'justify-end' => true || config('filament.layout.forms.actions.alignment') === 'right',
                    ])
                >
                    <x-filament::button color="gray" wire:click="closeRenameMediaFolderModal">
                        {{ \Illuminate\Support\Str::ucfirst(__('filament-media-library::translations.phrases.cancel')) }}
                    </x-filament::button>
                    <x-filament::button color="primary" wire:click="renameMediaFolder">
                        {{ \Illuminate\Support\Str::ucfirst(__('filament-media-library::translations.phrases.rename-folder')) }}
                    </x-filament::button>
                </div>
            </x-slot>
        </x-filament::modal>

        <x-filament::modal
            id="move-media-folder"
            :alignment="Alignment::Center"
            :footerActionsAlignment="Alignment::End"
            :heading="__('filament-media-library::translations.components.browse-library.modals.move-media-folder.heading')"
            :description="__('filament-media-library::translations.components.browse-library.modals.move-media-folder.subheading')"
            x-on:close-modal.window="if ($event.detail.id === 'move-media-folder') { $nextTick(() => document.getElementsByTagName('html')[0].style.overflow = null) }"
        >
            {{--
                For some reason, the Alpine.js `x-trap.noscroll` does not remove the scroll when closing the modal in this case.
                In the (revamped) v4 of this package I will refactor this to use Filament's native actions, which should fix
                this issue. Therefore, this temporary fix is for now the best way to handle this and restore the scrolling.
            --}}
            <form wire:submit.prevent="moveMediaFolder">{{ $this->moveMediaFolderForm }}</form>
            <x-slot name="footer">
                <div
                    @class([
                        'flex space-x-2 rtl:space-x-reverse',
                        //                        'justify-start' =>  config('filament.layout.forms.actions.alignment') === 'left',
                        //                        'justify-center' => config('filament.layout.forms.actions.alignment') === 'center',
                        'justify-end' => true || config('filament.layout.forms.actions.alignment') === 'right',
                    ])
                >
                    <x-filament::button color="gray" wire:click="closeMoveMediaFolderModal">
                        {{ \Illuminate\Support\Str::ucfirst(__('filament-media-library::translations.phrases.cancel')) }}
                    </x-filament::button>
                    <x-filament::button color="primary" wire:click="moveMediaFolder">
                        {{ \Illuminate\Support\Str::ucfirst(__('filament-media-library::translations.phrases.move-folder')) }}
                    </x-filament::button>
                </div>
            </x-slot>
        </x-filament::modal>
        <x-filament::modal
            id="delete-media-folder"
            :alignment="Alignment::Center"
            :footerActionsAlignment="Alignment::End"
            :heading="__('filament-media-library::translations.components.browse-library.modals.delete-media-folder.heading')"
            :description="__('filament-media-library::translations.components.browse-library.modals.delete-media-folder.subheading')"
            x-on:close-modal.window="if ($event.detail.id === 'delete-media-folder') { $nextTick(() => document.getElementsByTagName('html')[0].style.overflow = null) }"
        >
            {{--
                For some reason, the Alpine.js `x-trap.noscroll` does not remove the scroll when closing the modal in this case.
                In the (revamped) v4 of this package I will refactor this to use Filament's native actions, which should fix
                this issue. Therefore, this temporary fix is for now the best way to handle this and restore the scrolling.
            --}}
            <form>
                {{ $this->deleteMediaFolderForm }}
            </form>
            <x-slot name="footer">
                <div
                    @class([
                        'flex space-x-2 rtl:space-x-reverse',
                        //                        'justify-start' =>  config('filament.layout.forms.actions.alignment') === 'left',
                        //                        'justify-center' => config('filament.layout.forms.actions.alignment') === 'center',
                        'justify-end' => true || config('filament.layout.forms.actions.alignment') === 'right',
                    ])
                >
                    <x-filament::button color="gray" wire:click="closeDeleteMediaFolderModal">
                        {{ \Illuminate\Support\Str::ucfirst(__('filament-media-library::translations.phrases.cancel')) }}
                    </x-filament::button>
                    <x-filament::button color="danger" wire:click="deleteMediaFolder">
                        {{ \Illuminate\Support\Str::ucfirst(__('filament-media-library::translations.phrases.confirm')) }}
                    </x-filament::button>
                </div>
            </x-slot>
        </x-filament::modal>
    </div>
</div>

@script
    <script>
        Alpine.store('browseLibrary', {
            isMultiple: false,
            // Separate the single selection from the multiple selection, so that we don't have from and to arrays every time..
            selectedMediaItemId: null,
            selectedMediaItemIds: [],
            latestSelectedMediaItemId: null,

            selectMediaItem(mediaItemId) {
                if (this.isMultiple) {
                    if (typeof mediaItemId === 'object') {
                        mediaItemId = Object.values(mediaItemId)
                    }

                    this.selectedMediaItemIds = mediaItemId
                } else {
                    this.selectedMediaItemId = mediaItemId
                }

                this.latestSelectedMediaItemId = mediaItemId
            },

            selectMediaItems(mediaItemIds) {
                this.selectMediaItem(mediaItemIds)
            },

            toggleMediaItemSelection(mediaItemId) {
                if (!this.isMultiple) {
                    this.selectedMediaItemId = mediaItemId
                    this.latestSelectedMediaItemId = mediaItemId

                    return
                }

                // Loose comparison, so using some instead of includes
                if (this.selectedMediaItemIds.some((itemId) => itemId == mediaItemId)) {
                    this.selectedMediaItemIds = this.selectedMediaItemIds.filter((item) => item != mediaItemId)
                } else {
                    this.selectedMediaItemIds.push(mediaItemId)
                }

                this.latestSelectedMediaItemId = mediaItemId
            },

            isMediaItemSelected(mediaItemId) {
                if (!this.isMultiple) {
                    return this.selectedMediaItemId == mediaItemId // Loose comparison for string/integer.
                }

                for (let selectedMediaItemId of this.selectedMediaItemIds) {
                    if (selectedMediaItemId == mediaItemId) {
                        // Loose comparison for string/integer.
                        return true
                    }
                }

                return false
            },

            resetMediaItemsSelection() {
                this.selectedMediaItemId = null
                this.selectedMediaItemIds = []
                this.latestSelectedMediaItemId = null
            },
        })
    </script>
@endscript
