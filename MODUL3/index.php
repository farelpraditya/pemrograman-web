<?php

declare(strict_types=1);

require_once __DIR__ . '/src/Models/Media.php';
require_once __DIR__ . '/src/Models/Video.php';
require_once __DIR__ . '/src/Models/Reading.php';
require_once __DIR__ . '/src/Models/User.php';
require_once __DIR__ . '/src/Services/Library.php';

use FlipPlay\Models\Reading;
use FlipPlay\Models\User;
use FlipPlay\Models\Video;
use FlipPlay\Services\Library;

function readInput(string $label): string
{
    echo $label;
    $line = fgets(STDIN);

    if ($line === false) {
        echo PHP_EOL . "Input berakhir, program dihentikan" . PHP_EOL;
        exit(0);
    }

    return trim($line);
}

function readText(string $label): string
{
    while (true) {
        $value = readInput($label);

        if ($value !== '') {
            return $value;
        }

        echo "Input tidak boleh kosong" . PHP_EOL;
    }
}

function readNumber(string $label, int $min, int $max): int
{
    while (true) {
        $value = readInput($label);

        if (ctype_digit($value) && (int) $value >= $min && (int) $value <= $max) {
            return (int) $value;
        }

        echo "Masukkan angka antara {$min} sampai {$max}" . PHP_EOL;
    }
}

function readRating(): float
{
    while (true) {
        $value = readInput("Rating (0-10): ");

        if (is_numeric($value)) {
            return (float) $value;
        }

        echo "Rating harus berupa angka" . PHP_EOL;
    }
}

function printTitle(string $title): void
{
    echo PHP_EOL . "=== " . $title . " ===" . PHP_EOL;
}

function printNumberedList(array $mediaList): void
{
    $number = 1;

    foreach ($mediaList as $media) {
        echo $number . ". " . $media->getSummary() . PHP_EOL;
        $number++;
    }
}

function showMenu(string $userName): void
{
    printTitle("FlipPlay - Menu Utama (" . $userName . ")");
    echo "1. Tambah media ke library" . PHP_EOL;
    echo "2. Tampilkan daftar media" . PHP_EOL;
    echo "3. Cari media berdasarkan judul" . PHP_EOL;
    echo "4. Tampilkan detail media" . PHP_EOL;
    echo "5. Koleksi media saya" . PHP_EOL;
    echo "0. Keluar" . PHP_EOL;
}

function seedLibrary(Library $library): void
{
    $library->addMedia(new Video("Attack on Titan", "Aksi", 2013, "Anime", 9.0, 87, "WIT Studio"));
    $library->addMedia(new Video("Demon Slayer", "Petualangan", 2019, "Anime", 8.7, 44, "Ufotable"));
    $library->addMedia(new Video("Crash Landing on You", "Romantis", 2019, "Drama", 8.7, 16, "Korea Selatan"));
    $library->addMedia(new Video("Goblin", "Fantasi", 2016, "Drama", 8.6, 16, "Korea Selatan"));
    $library->addMedia(new Reading("One Piece", "Petualangan", 1997, "Manga", 9.2, 1100, "chapter", "Eiichiro Oda"));
    $library->addMedia(new Reading("Vagabond", "Aksi", 1998, "Manga", 9.1, 327, "chapter", "Takehiko Inoue"));
    $library->addMedia(new Reading("Sword Art Online", "Fantasi", 2009, "Light Novel", 7.9, 27, "volume", "Reki Kawahara"));
    $library->addMedia(new Reading("Re:Zero", "Isekai", 2012, "Light Novel", 8.4, 36, "volume", "Tappei Nagatsuki"));
}

function addMediaMenu(Library $library): void
{
    printTitle("Tambah Media ke Library");

    $categories = [1 => "Anime", 2 => "Drama", 3 => "Manga", 4 => "Light Novel"];

    foreach ($categories as $number => $name) {
        echo $number . ". " . $name . PHP_EOL;
    }

    $choice = readNumber("Pilih kategori (0 = batal): ", 0, 4);

    if ($choice === 0) {
        return;
    }

    $category = $categories[$choice];
    $title = readText("Judul: ");

    if ($library->findByTitle($title) !== null) {
        echo "Media dengan judul tersebut sudah ada di library" . PHP_EOL;
        return;
    }

    $genre = readText("Genre: ");
    $year = readNumber("Tahun rilis: ", 1900, 2100);
    $rating = readRating();
    $unit = "";

    if ($choice <= 2) {
        $total = readNumber("Jumlah episode: ", 1, 100000);
        $creator = readText($choice === 1 ? "Studio: " : "Negara asal: ");
    } else {
        $unit = $choice === 3 ? "chapter" : "volume";
        $total = readNumber("Jumlah {$unit}: ", 1, 100000);
        $creator = readText("Penulis: ");
    }

    try {
        $media = $choice <= 2
            ? new Video($title, $genre, $year, $category, $rating, $total, $creator)
            : new Reading($title, $genre, $year, $category, $rating, $total, $unit, $creator);

        $library->addMedia($media);
        echo "Berhasil ditambahkan: " . $media->getSummary() . PHP_EOL;
        echo "Total media di library: " . $library->countMedia() . PHP_EOL;
    } catch (InvalidArgumentException $error) {
        echo "Gagal menambahkan media: " . $error->getMessage() . PHP_EOL;
    }
}

function listMediaMenu(Library $library): void
{
    printTitle("Daftar Media Tersedia");

    if ($library->countMedia() === 0) {
        echo "Library masih kosong" . PHP_EOL;
        return;
    }

    $filters = [1 => "Anime", 2 => "Drama", 3 => "Manga", 4 => "Light Novel"];

    echo "0. Semua kategori" . PHP_EOL;

    foreach ($filters as $number => $name) {
        echo $number . ". " . $name . PHP_EOL;
    }

    $choice = readNumber("Pilih kategori: ", 0, 4);

    printTitle($choice === 0 ? "Semua Media" : "Kategori " . $filters[$choice]);

    $mediaList = $choice === 0 ? $library->getAll() : $library->filterByCategory($filters[$choice]);

    if (count($mediaList) === 0) {
        echo "Belum ada media pada kategori ini" . PHP_EOL;
        return;
    }

    printNumberedList($mediaList);
}

function searchMenu(Library $library): void
{
    printTitle("Cari Media Berdasarkan Judul");

    $keyword = readText("Masukkan judul atau kata kunci: ");
    $results = $library->searchByTitle($keyword);

    if (count($results) === 0) {
        echo "Media dengan kata kunci \"{$keyword}\" tidak ditemukan" . PHP_EOL;
        return;
    }

    echo "Ditemukan " . count($results) . " media:" . PHP_EOL;
    printNumberedList($results);
}

function detailMenu(Library $library): void
{
    printTitle("Detail Media");

    if ($library->countMedia() === 0) {
        echo "Library masih kosong" . PHP_EOL;
        return;
    }

    $mediaList = $library->getAll();
    printNumberedList($mediaList);

    $choice = readNumber("Pilih nomor media (0 = batal): ", 0, count($mediaList));

    if ($choice === 0) {
        return;
    }

    $media = $mediaList[$choice - 1];

    echo PHP_EOL . $media->getSummary() . PHP_EOL;
    echo $media->getDetail() . PHP_EOL;
}

function collectionMenu(Library $library, User $user): void
{
    while (true) {
        printTitle("Koleksi Milik " . $user->getName());
        echo "1. Lihat koleksi" . PHP_EOL;
        echo "2. Tambah media dari library ke koleksi" . PHP_EOL;
        echo "3. Hapus media dari koleksi" . PHP_EOL;
        echo "0. Kembali ke menu utama" . PHP_EOL;

        $choice = readNumber("Pilih menu: ", 0, 3);

        if ($choice === 0) {
            return;
        }

        if ($choice === 1) {
            printTitle("Koleksi " . $user->getName());
            $user->listCollection();
        }

        if ($choice === 2) {
            addToCollectionMenu($library, $user);
        }

        if ($choice === 3) {
            removeFromCollectionMenu($user);
        }
    }
}

function addToCollectionMenu(Library $library, User $user): void
{
    if ($library->countMedia() === 0) {
        echo "Library masih kosong" . PHP_EOL;
        return;
    }

    $mediaList = $library->getAll();
    printNumberedList($mediaList);

    $choice = readNumber("Pilih nomor media (0 = batal): ", 0, count($mediaList));

    if ($choice === 0) {
        return;
    }

    $media = $mediaList[$choice - 1];

    if ($user->addToCollection($media)) {
        echo $media->getTitle() . " masuk ke koleksi" . PHP_EOL;
    } else {
        echo $media->getTitle() . " sudah ada di koleksi" . PHP_EOL;
    }
}

function removeFromCollectionMenu(User $user): void
{
    $collection = $user->getCollection();

    if (count($collection) === 0) {
        echo "Koleksi masih kosong" . PHP_EOL;
        return;
    }

    printNumberedList($collection);

    $choice = readNumber("Pilih nomor media yang dihapus (0 = batal): ", 0, count($collection));

    if ($choice === 0) {
        return;
    }

    $title = $collection[$choice - 1]->getTitle();
    $user->removeFromCollection($title);
    echo $title . " dihapus dari koleksi" . PHP_EOL;
}

echo "==============================" . PHP_EOL;
echo "   FlipPlay Backend Engine" . PHP_EOL;
echo "==============================" . PHP_EOL;

$name = readText("Nama pengguna: ");
$email = readText("Email: ");

$user = new User($name, $email);
$library = new Library();
seedLibrary($library);

echo "Selamat datang, " . $user->getName() . "!" . PHP_EOL;

while (true) {
    showMenu($user->getName());
    $choice = readNumber("Pilih menu: ", 0, 5);

    if ($choice === 0) {
        echo "Terima kasih telah menggunakan FlipPlay" . PHP_EOL;
        break;
    }

    switch ($choice) {
        case 1:
            addMediaMenu($library);
            break;
        case 2:
            listMediaMenu($library);
            break;
        case 3:
            searchMenu($library);
            break;
        case 4:
            detailMenu($library);
            break;
        case 5:
            collectionMenu($library, $user);
            break;
    }
}