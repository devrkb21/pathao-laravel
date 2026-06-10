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
        Schema::create('pathao_cities', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('pathao_zones', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('city_id');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('pathao_areas', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('zone_id');
            $table->string('name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pathao_areas');
        Schema::dropIfExists('pathao_zones');
        Schema::dropIfExists('pathao_cities');
    }
};
