<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Use raw SQL to avoid doctrine/dbal dependency
        DB::statement('ALTER TABLE media_images MODIFY image_url TEXT NOT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE media_images MODIFY image_url VARCHAR(255) NOT NULL');
    }
};
