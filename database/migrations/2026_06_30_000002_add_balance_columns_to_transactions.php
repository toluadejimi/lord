<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('transactions')) {
            return;
        }

        if (!Schema::hasColumn('transactions', 'balance')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->decimal('balance', 14, 2)->nullable();
            });
        }

        if (!Schema::hasColumn('transactions', 'old_balance')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->decimal('old_balance', 14, 2)->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'balance')) {
                $table->dropColumn('balance');
            }
            if (Schema::hasColumn('transactions', 'old_balance')) {
                $table->dropColumn('old_balance');
            }
        });
    }
};
