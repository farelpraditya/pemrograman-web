<?php

namespace Database\Seeders;

use App\Models\Media;
use Illuminate\Database\Seeder;

class MediaSeeder extends Seeder
{
    public function run(): void
    {
        $mediaList = [
            ['title' => 'Attack on Titan',       'genre' => 'Aksi',        'year' => 2013, 'category' => 'Anime',       'rating' => 9.0, 'total_parts' => 87,   'creator' => 'WIT Studio'],
            ['title' => 'Demon Slayer',          'genre' => 'Petualangan', 'year' => 2019, 'category' => 'Anime',       'rating' => 8.7, 'total_parts' => 44,   'creator' => 'Ufotable'],
            ['title' => 'Crash Landing on You',  'genre' => 'Romantis',    'year' => 2019, 'category' => 'Drama',       'rating' => 8.7, 'total_parts' => 16,   'creator' => 'Korea Selatan'],
            ['title' => 'Goblin',                'genre' => 'Fantasi',     'year' => 2016, 'category' => 'Drama',       'rating' => 8.6, 'total_parts' => 16,   'creator' => 'Korea Selatan'],
            ['title' => 'One Piece',             'genre' => 'Petualangan', 'year' => 1997, 'category' => 'Manga',       'rating' => 9.2, 'total_parts' => 1100, 'creator' => 'Eiichiro Oda'],
            ['title' => 'Vagabond',              'genre' => 'Aksi',        'year' => 1998, 'category' => 'Manga',       'rating' => 9.1, 'total_parts' => 327,  'creator' => 'Takehiko Inoue'],
            ['title' => 'Sword Art Online',      'genre' => 'Fantasi',     'year' => 2009, 'category' => 'Light Novel', 'rating' => 7.9, 'total_parts' => 27,   'creator' => 'Reki Kawahara'],
            ['title' => 'Re:Zero',               'genre' => 'Isekai',      'year' => 2012, 'category' => 'Light Novel', 'rating' => 8.4, 'total_parts' => 36,   'creator' => 'Tappei Nagatsuki'],
        ];

        foreach ($mediaList as $media) {
            Media::create($media);
        }
    }
}
