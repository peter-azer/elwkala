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
        Schema::create('products_packs_sizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('product_pack_id')->constrained('products_packs')->onDelete('cascade');
            $table->string('pack_size');
            $table->string('pack_name');
            $table->double('pack_price');
            $table->double('quantity')->default(0);
            $table->double('pack_price_discount_percentage')->nullable();
            $table->double('pack_price_discount')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products_packs_sizes');
    }
};
