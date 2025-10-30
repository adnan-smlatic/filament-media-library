<?php

namespace RalphJSmit\Filament\MediaLibrary\FilamentMediaLibrary;

use RalphJSmit\Filament\MediaLibrary\Media\Models\MediaLibraryFolder;
use RalphJSmit\Filament\MediaLibrary\Media\Models\MediaLibraryItem;

trait HasModelConfiguration
{
    protected string $modelItem = MediaLibraryItem::class;

    protected string $modelFolder = MediaLibraryFolder::class;

    /**
     * Use the below setting to customize the model used for media library items.
     * This allows you to override the model for an item and customize it.
     * Make sure to always extend the original model, so that you will not accidentally
     * lose functionality or forget to upgrade functions.
     */
    public function modelItem(string $className): static
    {
        $this->modelItem = $className;

        return $this;
    }

    public function modelFolder(string $className): static
    {
        $this->modelFolder = $className;

        return $this;
    }

    /**
     * @return class-string<MediaLibraryItem>
     */
    public function getModelItem(): string
    {
        return $this->modelItem;
    }

    /**
     * @return class-string<MediaLibraryFolder>
     */
    public function getModelFolder(): string
    {
        return $this->modelFolder;
    }
}
