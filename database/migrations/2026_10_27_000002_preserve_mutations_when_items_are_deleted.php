<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            $this->rebuildSqliteMutationsTable(true);

            return;
        }

        Schema::table('mutations', function (Blueprint $table) {
            $table->string('item_code')->nullable();
            $table->string('item_name')->nullable();
        });

        DB::statement(
            'UPDATE mutations SET item_code = (SELECT code FROM items WHERE items.id = mutations.item_id), '
            .'item_name = (SELECT name FROM items WHERE items.id = mutations.item_id)'
        );

        Schema::table('mutations', function (Blueprint $table) {
            $table->dropForeign(['item_id']);
        });

        Schema::table('mutations', function (Blueprint $table) {
            $table->unsignedBigInteger('item_id')->nullable()->change();
        });

        Schema::table('mutations', function (Blueprint $table) {
            $table->foreign('item_id')->references('id')->on('items')->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (DB::table('mutations')->whereNull('item_id')->exists()) {
            throw new RuntimeException(
                'Cannot roll back item deletion history while mutations reference deleted items.'
            );
        }

        if (DB::getDriverName() === 'sqlite') {
            $this->rebuildSqliteMutationsTable(false);

            return;
        }

        Schema::table('mutations', function (Blueprint $table) {
            $table->dropForeign(['item_id']);
        });

        Schema::table('mutations', function (Blueprint $table) {
            $table->unsignedBigInteger('item_id')->nullable(false)->change();
        });

        Schema::table('mutations', function (Blueprint $table) {
            $table->foreign('item_id')->references('id')->on('items')->restrictOnDelete();
            $table->dropColumn(['item_code', 'item_name']);
        });
    }

    private function rebuildSqliteMutationsTable(bool $preserveItemSnapshots): void
    {
        Schema::create('mutations_replacement', function (Blueprint $table) use ($preserveItemSnapshots) {
            $table->id();
            $item = $table->foreignId('item_id')->nullable($preserveItemSnapshots)->constrained();
            if ($preserveItemSnapshots) {
                $item->nullOnDelete();
            } else {
                $item->restrictOnDelete();
            }
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            if ($preserveItemSnapshots) {
                $table->string('item_code')->nullable();
                $table->string('item_name')->nullable();
            }
            $table->string('type', 10);
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('previous_stock');
            $table->unsignedInteger('current_stock');
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        $columns = [
            'id',
            'item_id',
            'user_id',
        ];
        $source = DB::table('mutations as mutation')
            ->leftJoin('items', 'items.id', '=', 'mutation.item_id')
            ->select([
                'mutation.id',
                'mutation.item_id',
                'mutation.user_id',
            ]);

        if ($preserveItemSnapshots) {
            $columns[] = 'item_code';
            $columns[] = 'item_name';
            $source->addSelect(['items.code as item_code', 'items.name as item_name']);
        }

        array_push(
            $columns,
            'type',
            'quantity',
            'previous_stock',
            'current_stock',
            'notes',
            'created_at'
        );
        $source->addSelect([
            'mutation.type',
            'mutation.quantity',
            'mutation.previous_stock',
            'mutation.current_stock',
            'mutation.notes',
            'mutation.created_at',
        ]);

        DB::table('mutations_replacement')->insertUsing($columns, $source);
        Schema::drop('mutations');
        Schema::rename('mutations_replacement', 'mutations');
    }
};
