<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('title_ru');
            $table->string('title_tk');
            $table->longText('body_ru');
            $table->longText('body_tk');
            $table->string('image_path')->nullable();
            $table->enum('type', ['regular', 'advertising'])->default('regular');
            $table->string('link_type')->nullable();
            $table->unsignedBigInteger('link_id')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
