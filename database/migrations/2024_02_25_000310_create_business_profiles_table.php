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
        Schema::create('business_profiles', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->integer('business_types_id');
            $table->unsignedBigInteger('pi_users_id');
            $table->string('pi_users_username')->nullable();
            $table->string('name');
            $table->text('location')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->json('business_profile_photos')->nullable();
            $table->boolean('menu_status')->default(true);
            $table->boolean('orders_status')->default(true);
            $table->boolean('payments_status')->default(true);
            $table->boolean('loyalty_card_status')->default(true);
            $table->json('params')->nullable();
            $table->boolean('verified')->default(0);
            $table->softDeletes();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_profiles');
    }
};
