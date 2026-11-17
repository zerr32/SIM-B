<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->string('unit', 50)->default('pcs');
        });

        Schema::table('mutations', function (Blueprint $table) {
            $table->string('unit', 50)->nullable();
            $table->string('recipient_name', 150)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('mutations', function (Blueprint $table) {
            $table->dropColumn(['unit', 'recipient_name']);
        });

        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('unit');
        });
    }
};
