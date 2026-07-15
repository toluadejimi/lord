<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('esim_orders')) {
            Schema::create('esim_orders', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->string('ref_id')->unique();
                $table->string('provider_order_id')->nullable()->index();
                $table->string('package_code');
                $table->string('package_name')->nullable();
                $table->string('location')->nullable();
                $table->decimal('volume_gb', 10, 2)->nullable();
                $table->unsignedInteger('duration_days')->nullable();
                $table->decimal('amount_ngn', 12, 2);
                $table->decimal('amount_usd', 12, 4)->nullable();
                $table->unsignedInteger('provider_price_cents')->nullable();
                $table->string('status', 20)->default('processing')->index();
                $table->string('iccid')->nullable()->index();
                $table->text('qr_code_url')->nullable();
                $table->text('activation_code')->nullable();
                $table->text('short_url')->nullable();
                $table->string('esim_status')->nullable();
                $table->text('failure_reason')->nullable();
                $table->json('provider_response')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('settings') && !DB::table('settings')->where('id', 8)->exists()) {
            DB::table('settings')->insert([
                'id' => 8,
                'name' => 'esim',
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
        Schema::dropIfExists('esim_orders');
    }
};
