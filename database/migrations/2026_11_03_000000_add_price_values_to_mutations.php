<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mutations', function (Blueprint $table) {
            $table->decimal('unit_price', 15, 2)->nullable();
            $table->decimal('total_price', 25, 2)->nullable();
            $table->decimal('remaining_value', 25, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('mutations', function (Blueprint $table) {
            $table->dropColumn(['unit_price', 'total_price', 'remaining_value']);
        });
    }
};
