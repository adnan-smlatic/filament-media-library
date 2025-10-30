<?php

namespace RalphJSmit\Filament\MediaLibrary\FilamentMediaLibrary;

use Closure;
use Illuminate\Contracts\Support\Htmlable;

trait HasNavigationConfiguration
{
    protected null | string | Closure $navigationGroup = null;

    protected ?int $navigationSort = null;

    protected ?string $navigationIcon = null;

    protected ?string $activeNavigationIcon = null;

    protected null | string | Closure $navigationLabel = null;

    protected null | string | Htmlable | Closure $title = null;

    protected ?string $slug = null;

    public function navigationGroup(null | string | Closure $navigationGroup): static
    {
        $this->navigationGroup = $navigationGroup;

        return $this;
    }

    public function getNavigationGroup(): ?string
    {
        return value($this->navigationGroup);
    }

    public function navigationSort(int $navigationSort): static
    {
        $this->navigationSort = $navigationSort;

        return $this;
    }

    public function getNavigationSort(): ?int
    {
        return $this->navigationSort;
    }

    public function navigationIcon(?string $navigationIcon): static
    {
        $this->navigationIcon = $navigationIcon;

        return $this;
    }

    public function getNavigationIcon(): ?string
    {
        return $this->navigationIcon;
    }

    public function activeNavigationIcon(?string $activeNavigationIcon): static
    {
        $this->activeNavigationIcon = $activeNavigationIcon;

        return $this;
    }

    public function getActiveNavigationIcon(): ?string
    {
        return $this->activeNavigationIcon ?? $this->getNavigationIcon();
    }

    public function navigationLabel(null | string | Closure $navigationLabel): static
    {
        $this->navigationLabel = $navigationLabel;

        return $this;
    }

    public function getNavigationLabel(): ?string
    {
        return value($this->navigationLabel);
    }

    public function pageTitle(string | Htmlable | Closure $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getPageTitle(): string | Htmlable | null
    {
        return value($this->title);
    }

    public function slug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }
}
