<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('app_configs')) {
            Schema::create('app_configs', function (Blueprint $table) {
                $table->id();
                $table->string('config_key')->unique();
                $table->text('config_value')->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'api_key')) {
                    $table->string('api_key', 64)->nullable()->unique()->after('wallet');
                }
                if (!Schema::hasColumn('users', 'webhook_url')) {
                    $table->string('webhook_url')->nullable()->after('api_key');
                }
                if (!Schema::hasColumn('users', 'api_percentage')) {
                    $table->decimal('api_percentage', 8, 4)->default(1)->after('webhook_url');
                }
                if (!Schema::hasColumn('users', 'phone')) {
                    $table->string('phone')->nullable()->after('name');
                }
            });
        }

        if (Schema::hasTable('settings')) {
            Schema::table('settings', function (Blueprint $table) {
                if (!Schema::hasColumn('settings', 'is_enabled')) {
                    $table->boolean('is_enabled')->default(true)->after('margin');
                }
            });
        }

        if (Schema::hasTable('settings') && !DB::table('settings')->where('id', 5)->exists()) {
            DB::table('settings')->insert([
                ['id' => 5, 'name' => 'hero', 'rate' => 0, 'margin' => 0, 'is_enabled' => true, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 6, 'name' => 'sv3', 'rate' => 0, 'margin' => 0, 'is_enabled' => true, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        if (Schema::hasTable('settings')) {
            DB::table('settings')->where('id', 4)->update(['name' => 'usa2']);
        }

        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->text('message')->nullable();
                $table->boolean('is_active')->default(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('webhook_responses')) {
            Schema::create('webhook_responses', function (Blueprint $table) {
                $table->id();
                $table->string('order_id')->nullable()->index();
                $table->unsignedSmallInteger('response_code')->nullable();
                $table->text('response_body')->nullable();
                $table->string('url')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('verification_sms')) {
            Schema::create('verification_sms', function (Blueprint $table) {
                $table->id();
                $table->foreignId('verification_id')->constrained('verifications')->cascadeOnDelete();
                $table->text('sms')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('countries')) {
            Schema::create('countries', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('country_id')->nullable()->index();
                $table->string('short_name')->nullable();
                $table->string('name')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('slug')->nullable();
                $table->boolean('status')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
                $table->id();
                $table->foreignId('category_id')->nullable();
                $table->string('title')->nullable();
                $table->decimal('amount', 12, 2)->default(0);
                $table->integer('qty')->default(0);
                $table->boolean('status')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('items')) {
            Schema::create('items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('category_id')->nullable();
                $table->string('title')->nullable();
                $table->decimal('amount', 12, 2)->default(0);
                $table->integer('qty')->default(0);
                $table->boolean('status')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('main_items')) {
            Schema::create('main_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('item_id')->nullable();
                $table->text('content')->nullable();
                $table->boolean('sold')->default(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('sold_logs')) {
            Schema::create('sold_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable();
                $table->foreignId('item_id')->nullable();
                $table->string('file_path')->nullable();
                $table->decimal('amount', 12, 2)->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('payment_points')) {
            Schema::create('payment_points', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('email')->nullable();
                $table->string('account_no')->nullable();
                $table->string('bank_name')->nullable();
                $table->string('account_name')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('wallet_checks')) {
            Schema::create('wallet_checks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->unique();
                $table->decimal('total_funded', 14, 2)->default(0);
                $table->decimal('wallet_amount', 14, 2)->default(0);
                $table->decimal('total_bought', 14, 2)->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_checks');
        Schema::dropIfExists('payment_points');
        Schema::dropIfExists('sold_logs');
        Schema::dropIfExists('main_items');
        Schema::dropIfExists('items');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('countries');
        Schema::dropIfExists('verification_sms');
        Schema::dropIfExists('webhook_responses');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('app_configs');

        Schema::table('users', function (Blueprint $table) {
            $cols = ['api_key', 'webhook_url', 'api_percentage', 'phone'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        if (Schema::hasColumn('settings', 'is_enabled')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->dropColumn('is_enabled');
            });
        }
    }
};
