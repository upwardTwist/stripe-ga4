<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_api_keys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('stripe_key')->nullable();
            $table->text('stripe_secret')->nullable();
            $table->text('stripe_webhook_secret')->nullable();
            $table->text('ga4_measurement_protocol')->nullable();
            $table->text('ga4_measurement_id')->nullable();
            $table->text('ga4_api_secret')->nullable();
            $table->text('webhook_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_api_keys');
    }
};
