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
        Schema::create('pi_users', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('uid');
            $table->string('username');
            $table->string('email')->unique()->nullable();
            $table->string('email_verification_code')->nullable();
            $table->boolean('is_partner')->default(false);
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('shop_name')->nullable();
            $table->string('referred_by')->nullable();
            $table->string('password')->nullable();
            //$table->string('remember_token');
            $table->rememberToken();
            //$table->text('acessToken');
            $table->text('public_key')->nullable();
            $table->json('roles')->nullable();
            $table->json('permissions')->nullable();
            $table->string('locale');
            $table->integer('last_message_id')->nullable();
            $table->json('cart')->nullable();
            $table->json('addresses')->nullable();
            $table->string('avatar')->nullable();
            $table->string('address')->nullable();
            $table->string('living_address')->nullable();
            $table->timestamp('last_notification_page_refresh')->useCurrent();
            $table->boolean('verified')->default(false);
            $table->boolean('active')->default(true)->comment("Active or deactive state for pioneers account");
            $table->integer('last_support_message_id')->nullable();
            $table->json('filter_country_code')->nullable();
            $table->json('partner_country_code')->nullable();
            $table->softDeletes();
            $table->integer('delivery_penalties')->default(0);
            $table->json('mining_data')->nullable();
            $table->timestamp('last_mining_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('email_code_generated_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pi_users');
    }
};
