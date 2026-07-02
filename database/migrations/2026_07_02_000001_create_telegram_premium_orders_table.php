<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('telegram_premium_orders')) {
            Schema::create('telegram_premium_orders', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->string('istar_order_id')->nullable()->index();
                $table->string('ref_id')->unique();
                $table->string('username');
                $table->string('recipient_hash')->nullable();
                $table->string('recipient_name')->nullable();
                $table->unsignedTinyInteger('months');
                $table->decimal('amount_ngn', 12, 2);
                $table->decimal('amount_usd', 12, 4)->nullable();
                $table->string('status', 20)->default('pending')->index();
                $table->string('tx_hash')->nullable();
                $table->text('failure_reason')->nullable();
                $table->json('provider_response')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('settings') && !DB::table('settings')->where('id', 7)->exists()) {
            DB::table('settings')->insert([
                'id' => 7,
                'name' => 'telegram_premium',
                'rate' => 0,
                'margin' => 0,
                'is_enabled' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('telegram_premium_orders');
    }
};
