<?php

declare(strict_types=1);

namespace FlipPlay\Models;

use InvalidArgumentException;

abstract class Media
{
    private float $rating = 0.0;

    public function __construct(
        protected string $title,
        protected string $genre,
        protected int $year,
        protected string $category,
        float $rating
    ) {
        $this->setRating($rating);
    }

    abstract public function getDetail(): string;

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getGenre(): string
    {
        return $this->genre;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getRating(): float
    {
        return $this->rating;
    }

    public function setRating(float $rating): void
    {
        if ($rating < 0 || $rating > 10) {
            throw new InvalidArgumentException("Rating harus di antara 0 dan 10");
        }

        $this->rating = $rating;
    }

    public function getSummary(): string
    {
        return sprintf(
            "[%s] %s (%d) - %s - Rating %.1f",
            $this->category,
            $this->title,
            $this->year,
            $this->genre,
            $this->rating
        );
    }
}
