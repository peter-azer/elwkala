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
        Schema::create('markets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('phone2')->nullable();
            $table->string('phone3')->nullable();
            $table->string('owner_name');
            $table->string('manager_name')->nullable();
            $table->string('market_name');
            $table->string('address');
            $table->integer('max_order_quantity')->default(1);
            $table->foreignId('area_id')->constrained('areas')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('markets');
    }
};
