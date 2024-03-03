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
        Schema::create('products', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('type')->nullable();
            $table->string('libelle');
            $table->text('description');
            $table->double('price');
            $table->string('price_str');
            $table->integer('quantity')->nullable();
            $table->integer('quantity_selling')->nullable();
            $table->boolean('free_shipping');
            $table->boolean('is_digital')->default(false);
            $table->text('address');
            $table->string('email')->nullable();
            $table->json('shipping_zone');
            $table->json('free_shipping_zone');
            $table->json('images');
            $table->integer('categories_id');
            $table->string('country_code');
            $table->string('city');
            $table->unsignedBigInteger('pi_users_id');
            $table->string('pi_users_username');
            $table->enum('status', ['pending', 'validated', 'rejected'])->default('pending');
            $table->boolean('active')->default(1)->comment("For admin: deactive a product for a reason");
            $table->boolean('visible')->default(1)->comment("For seller: hide a product for a reason");
            $table->softDeletes();
            $table->timestamp('auto_deactivated_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
