<?php

namespace RalphJSmit\Filament\MediaLibrary\Tables\Columns;

use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use RalphJSmit\Filament\MediaLibrary\FilamentMediaLibrary;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Throwable;

class MediaColumn extends SpatieMediaLibraryImageColumn
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->conversion('thumb');
    }

    public function getName(): string
    {
        $name = parent::getName();

        if (! Str::of($name)->contains('.media')) {
            $name .= '.media';
        }

        return $name;
    }

    public function getCollection(): ?string
    {
        $record = $this->getRecord();

        if ($this->hasRelationship($record)) {
            $record = $this->getRelationshipResults($record);
        }

        $record = Collection::wrap($record)->first();

        return $record?->getMediaLibraryCollectionName()
            ?? (new (FilamentMediaLibrary::get()->getModelItem())())->getMediaLibraryCollectionName();
    }

    public function getImageUrl(?string $state = null): ?string
    {
        $record = $this->getRecord();

        if ($this->hasRelationship($record)) {
            $record = $this->getRelationshipResults($record);
        }

        $media = Collection::wrap($record)->load('media')->pluck('media')->flatten(1);

        /** @var ?Media $media */
        $media = $media->first(fn (Media $media): bool => $media->uuid === $state);

        if (! $media) {
            return null;
        }

        if (FilamentMediaLibrary::get()->getDiskVisibility() === 'private') {
            try {
                return $media->getTemporaryUrl(
                    now()->addMinutes(5),
                    $this->getConversion(),
                );
            } catch (Throwable $exception) {
                // This driver does not support creating temporary URLs.
            }
        }

        return $media->getUrl($this->getConversion());
    }

    /**
     * @return array<string>
     */
    public function getState(): array
    {
        $collection = $this->getCollection();

        $record = $this->getRecord();

        if ($this->hasRelationship($record)) {
            $record = $this->getRelationshipResults($record);
        }

        $record = Collection::wrap($record);

        if ($record->isEmpty()) {
            return [];
        }

        return $record
            ->map(fn (Model $record) => $record->getRelationValue('media'))
            ->flatten(1)
            ->filter(fn (Media $media): bool => blank($collection) || ($media->getAttributeValue('collection_name') === $collection))
            ->map(fn (Media $media): string => $media->uuid)
            ->all();
    }
}
