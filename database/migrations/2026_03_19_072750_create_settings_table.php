<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('group');              // razorpay | smtp | general | branding
            $table->string('key')->unique();      // e.g. razorpay_key_id
            $table->string('label');              // Human-readable label
            $table->text('value')->nullable();    // The actual value
            $table->string('type')->default('text'); // text | password | boolean | textarea | number
            $table->boolean('is_secret')->default(false); // mask in API responses
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
