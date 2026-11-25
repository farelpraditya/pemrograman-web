<?php

declare(strict_types=1);

namespace FlipPlay\Services;

use FlipPlay\Models\Media;

class Library
{
    private array $mediaList = [];

    public function addMedia(Media $media): void
    {
        $this->mediaList[] = $media;
    }

    public function getAll(): array
    {
        return $this->mediaList;
    }

    public function countMedia(): int
    {
        return count($this->mediaList);
    }

    public function listMedia(): void
    {
        $number = 1;

        foreach ($this->mediaList as $media) {
            echo $number . ". " . $media->getSummary() . PHP_EOL;
            $number++;
        }
    }

    public function filterByCategory(string $category): array
    {
        return array_values(
            array_filter(
                $this->mediaList,
                fn(Media $media): bool => strcasecmp($media->getCategory(), $category) === 0
            )
        );
    }

    public function searchByTitle(string $keyword): array
    {
        return array_values(
            array_filter(
                $this->mediaList,
                fn(Media $media): bool => stripos($media->getTitle(), $keyword) !== false
            )
        );
    }

    public function findByTitle(string $title): ?Media
    {
        foreach ($this->mediaList as $media) {
            if (strcasecmp($media->getTitle(), $title) === 0) {
                return $media;
            }
        }

        return null;
    }
}
