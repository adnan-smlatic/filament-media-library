<?php

namespace RalphJSmit\Filament\MediaLibrary\Database\Factories\Media;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use RalphJSmit\Filament\MediaLibrary\FilamentMediaLibrary;
use RalphJSmit\Filament\MediaLibrary\Media\Models\MediaLibraryItem;

class MediaLibraryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uploaded_by_user_id' => null,
            'caption' => Arr::random([null, $this->faker->sentences(1, true)]),
        ];
    }

    public function modelName()
    {
        return FilamentMediaLibrary::get()->getModelItem();
    }

    public function withMedia(): static
    {
        return $this->afterCreating(function (MediaLibraryItem $media) {
            $uploadedFile = UploadedFile::fake()->image('image.jpg');

            $media
                ->addMedia($uploadedFile)
                ->toMediaCollection('library')
                ->save();
        });
    }
}
