<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Invoice fields (populated after successful payment)
            $table->string('invoice_number')->nullable()->after('notes');
            $table->string('invoice_path')->nullable()->after('invoice_number');  // storage path

            // Price-on-call: vendor assigns a final confirmed price before checkout
            $table->decimal('vendor_quoted_price', 12, 2)->nullable()->after('total_price');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['invoice_number', 'invoice_path', 'vendor_quoted_price']);
        });
    }
};
