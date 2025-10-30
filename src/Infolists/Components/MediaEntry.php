<?php

namespace RalphJSmit\Filament\MediaLibrary\Infolists\Components;

use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use RalphJSmit\Filament\MediaLibrary\FilamentMediaLibrary;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaEntry extends SpatieMediaLibraryImageEntry
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->conversion('thumb');

        $this->defaultImageUrl(null);
    }

    public function getName(): string
    {
        $name = parent::getName();

        if (! Str::of($name)->contains('.media')) {
            $name .= '.media';
        }

        return $name;
    }

    public function getStatePath(bool $isAbsolute = true): string
    {
        $statePath = parent::getStatePath($isAbsolute);

        if (! Str::of($statePath)->contains('.media')) {
            $statePath .= '.media';
        }

        return $statePath;
    }

    public function getCollection(): ?string
    {
        $record = $this->getRecord();

        if ($record && $this->getRelationshipName()) {
            $record = $record->getRelationValue($this->getRelationshipName());
        }

        if ($record instanceof Collection) {
            $record = $record->first();
        }

        return $record?->getMediaLibraryCollectionName()
            ?? (new (FilamentMediaLibrary::get()->getModelItem())())->getMediaLibraryCollectionName();
    }

    public function getImageUrl(?string $state = null): ?string
    {
        $record = $this->getRecord();

        $mediaRecord = null;

        if ($relationshipName = $this->getRelationshipName()) {
            $mediaRecord = $record->getRelationValue($this->getRelationshipName());
        }

        if (! $mediaRecord) {
            $originalRelationshipName = str($relationshipName)->beforeLast('.media');

            // The value comes from an attribute on the record. The `getRelationshipName()` always appends ".media",
            // so this component will look for a relationship by default. However, we need to circumvent that in
            // cases of IDs coming from attributes manually find the media records that belong to these IDs.
            if ($originalRelationshipName->isNotEmpty() && ! $originalRelationshipName->contains('.')) {
                $mediaRecord = Media::findByUuid($state);
            }
        }

        if ($mediaRecord instanceof Collection) {
            $media = $mediaRecord->pluck('media')->flatten(1);
        } elseif ($mediaRecord instanceof Media) {
            // The media record is already a media model, necessary for when the value comes from an attribute on the record.
            $media = new Collection([$mediaRecord]);
        } else {
            $media = $mediaRecord->media;
        }

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

        $mediaRecord = null;

        if ($relationshipName = $this->getRelationshipName()) {
            $mediaRecord = $record->getRelationValue($this->getRelationshipName());
        }

        if (! $mediaRecord) {
            $originalRelationshipName = str($relationshipName)->beforeLast('.media');

            // The value comes from an attribute on the record. The `getRelationshipName()` always appends ".media",
            // so this component will look for a relationship by default. However, we need to circumvent that in
            // cases of IDs coming from attributes manually find the media records that belong to these IDs.
            if ($originalRelationshipName->isNotEmpty() && ! $originalRelationshipName->contains('.')) {
                $state = $record->getAttributeValue($originalRelationshipName->toString());

                if ($state) {
                    $state = Arr::wrap($state);

                    $mediaRecord = FilamentMediaLibrary::get()->getModelItem()::find($state);
                }
            }
        }

        if (! $mediaRecord) {
            return [];
        }

        if (! $mediaRecord instanceof Collection) {
            $mediaRecord = new Collection([$mediaRecord]);
        }

        return $mediaRecord
            ->load('media')
            ->map(fn (Model $record) => $record->getRelationValue('media'))
            ->flatten(1)
            ->filter(fn (Media $media): bool => blank($collection) || ($media->getAttributeValue('collection_name') === $collection))
            ->map(fn (Media $media): string => $media->uuid)
            ->all();
    }
}
