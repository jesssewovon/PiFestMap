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
        Schema::create('awarded_stamps', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->integer('business_profiles_id');
            $table->integer('pi_users_id');
            $table->integer('nb_stamps_awarded');
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
        Schema::dropIfExists('awarded_stamps');
    }
};
