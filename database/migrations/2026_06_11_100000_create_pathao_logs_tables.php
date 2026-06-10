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
        Schema::create('pathao_api_logs', function (Blueprint $table) {
            $table->id();
            $table->string('api_endpoint');
            $table->string('method', 10);
            $table->text('payload')->nullable();
            $table->integer('response_status')->nullable();
            $table->text('response_body')->nullable();
            $table->timestamps();
        });

        Schema::create('pathao_webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->string('event')->nullable();
            $table->string('consignment_id')->nullable();
            $table->string('merchant_order_id')->nullable();
            $table->text('payload');
            $table->boolean('signature_valid');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pathao_webhook_logs');
        Schema::dropIfExists('pathao_api_logs');
    }
};
