<?php

declare(strict_types=1);

namespace FlipPlay\Models;

class Reading extends Media
{
    public function __construct(
        string $title,
        string $genre,
        int $year,
        string $category,
        float $rating,
        private int $totalParts,
        private string $unit,
        private string $author
    ) {
        parent::__construct($title, $genre, $year, $category, $rating);
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getDetail(): string
    {
        return "{$this->title} ({$this->category}) karya {$this->author} memiliki {$this->totalParts} {$this->unit}. Siap dibaca.";
    }
}
