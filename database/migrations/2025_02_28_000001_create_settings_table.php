<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('settings')) {
            return;
        }

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->decimal('rate', 10, 4)->default(0);
            $table->decimal('margin', 10, 4)->default(0);
            $table->timestamps();
        });

        DB::table('settings')->insert([
            ['id' => 1, 'name' => 'smspool', 'rate' => 0, 'margin' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'world', 'rate' => 0, 'margin' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'sim', 'rate' => 0, 'margin' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'name' => 'daisy', 'rate' => 0, 'margin' => 0, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
