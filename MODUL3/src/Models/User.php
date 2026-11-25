<?php

declare(strict_types=1);

namespace FlipPlay\Models;

class User
{
    private array $collection = [];

    public function __construct(
        private string $name,
        private string $email
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function addToCollection(Media $media): bool
    {
        $key = strtolower($media->getTitle());

        if (isset($this->collection[$key])) {
            return false;
        }

        $this->collection[$key] = $media;

        return true;
    }

    public function removeFromCollection(string $title): bool
    {
        $key = strtolower($title);

        if (!isset($this->collection[$key])) {
            return false;
        }

        unset($this->collection[$key]);

        return true;
    }

    public function getCollection(): array
    {
        return array_values($this->collection);
    }

    public function listCollection(): void
    {
        if (count($this->collection) === 0) {
            echo "Koleksi masih kosong" . PHP_EOL;
            return;
        }

        $number = 1;

        foreach ($this->collection as $media) {
            echo $number . ". " . $media->getSummary() . PHP_EOL;
            $number++;
        }
    }
}
