<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_group_id')->constrained()->onDelete('cascade');
            $table->string('name');              // e.g. "OOH"
            $table->string('subtitle')->nullable(); // e.g. "Billboards, Transit"
            $table->string('image_url')->nullable(); // Full URL (Unsplash or uploaded)
            $table->string('media_type_filter')->nullable(); // filter value for /media?type=
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
