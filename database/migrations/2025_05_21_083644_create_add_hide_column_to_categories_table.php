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
        Schema::table('categories', function (Blueprint $table) {
            $table->boolean('hide')->default(false);
        });
        Schema::table('sub_categories', function (Blueprint $table) {
            $table->boolean('hide')->default(false);
        });
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('hide')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('hide');
        });
        Schema::table('sub_categories', function (Blueprint $table) {
            $table->dropColumn('hide');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('hide');
        });
    }
};
