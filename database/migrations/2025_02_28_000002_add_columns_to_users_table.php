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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->nullable()->after('email');
            $table->unsignedBigInteger('role_id')->nullable()->after('remember_token');
            $table->decimal('wallet', 12, 2)->default(0)->after('role_id');
            $table->decimal('hold_wallet', 12, 2)->default(0)->after('wallet');
            $table->string('session_id')->nullable()->after('hold_wallet');
            $table->string('code')->nullable()->after('session_id');
            $table->tinyInteger('status')->default(1)->after('code');
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
