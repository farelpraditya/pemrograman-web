<?php

declare(strict_types=1);

namespace FlipPlay\Models;

class Video extends Media
{
    public function __construct(
        string $title,
        string $genre,
        int $year,
        string $category,
        float $rating,
        private int $episodes,
        private string $producer
    ) {
        parent::__construct($title, $genre, $year, $category, $rating);
    }

    public function getEpisodes(): int
    {
        return $this->episodes;
    }

    public function getDetail(): string
    {
        return "{$this->title} ({$this->category}) produksi {$this->producer} dengan {$this->episodes} episode. Siap ditonton.";
    }
}
