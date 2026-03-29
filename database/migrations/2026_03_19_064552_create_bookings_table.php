<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('media_id')->constrained('media')->cascadeOnDelete();
            $table->string('booking_reference')->unique();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('total_price', 12, 2)->nullable(); // nullable for price_on_call
            $table->boolean('price_on_call')->default(false);  // flagged if media has call pricing
            $table->unsignedInteger('quantity')->default(1);   // for unit/cpm pricing
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();                 // any user notes
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
