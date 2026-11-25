<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();
            $table->string('genre', 100);
            $table->unsignedSmallInteger('year');
            $table->string('category', 50);
            $table->decimal('rating', 3, 1)->default(0);
            $table->unsignedInteger('total_parts');
            $table->string('creator');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
