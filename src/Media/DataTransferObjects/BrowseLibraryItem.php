<?php

namespace RalphJSmit\Filament\MediaLibrary\Media\DataTransferObjects;

use Illuminate\Support\Carbon;
use RalphJSmit\Filament\MediaLibrary\Media\Models\MediaLibraryFolder;
use RalphJSmit\Filament\MediaLibrary\Media\Models\MediaLibraryItem;

class BrowseLibraryItem
{
    public function __construct(
        public MediaLibraryItem | MediaLibraryFolder $item,
    ) {}

    public function isMediaLibraryItem(): bool
    {
        return $this->item instanceof MediaLibraryItem;
    }

    public function isMediaLibraryFolder(): bool
    {
        return $this->item instanceof MediaLibraryFolder;
    }

    public function getChildrenCount(): int
    {
        if ($this->isMediaLibraryItem()) {
            return 0;
        }

        return $this->item->children_count + $this->item->media_library_items_count;
    }

    public function getCreatedAt(): Carbon
    {
        return $this->item->created_at;
    }

    public function getName(): string
    {
        if ($this->isMediaLibraryFolder()) {
            return $this->item->name;
        }

        return $this->item->getItem()->name;
    }
}
