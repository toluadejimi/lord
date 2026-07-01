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
        if (Schema::hasTable('verifications')) {
            return;
        }

        Schema::create('verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('phone');
            $table->string('order_id')->index();
            $table->string('country')->nullable();
            $table->string('service')->nullable();
            $table->decimal('cost', 10, 2)->default(0);
            $table->decimal('api_cost', 10, 2)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->string('ip', 45)->nullable();
            $table->tinyInteger('type')->default(1);
            $table->integer('expires_in')->nullable();
            $table->string('sms')->nullable();
            $table->text('full_sms')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verifications');
    }
};
