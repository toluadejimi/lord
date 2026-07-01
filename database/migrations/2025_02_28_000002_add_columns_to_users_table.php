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
        if (!Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->unique()->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'role_id')) {
                $table->unsignedBigInteger('role_id')->nullable()->after('remember_token');
            }
            if (!Schema::hasColumn('users', 'wallet')) {
                $table->decimal('wallet', 12, 2)->default(0)->after('role_id');
            }
            if (!Schema::hasColumn('users', 'hold_wallet')) {
                $table->decimal('hold_wallet', 12, 2)->default(0)->after('wallet');
            }
            if (!Schema::hasColumn('users', 'session_id')) {
                $table->string('session_id')->nullable()->after('hold_wallet');
            }
            if (!Schema::hasColumn('users', 'code')) {
                $table->string('code')->nullable()->after('session_id');
            }
            if (!Schema::hasColumn('users', 'status')) {
                $table->tinyInteger('status')->default(1)->after('code');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'username', 'role_id', 'wallet', 'hold_wallet',
                'session_id', 'code', 'status'
            ]);
        });
    }
};
