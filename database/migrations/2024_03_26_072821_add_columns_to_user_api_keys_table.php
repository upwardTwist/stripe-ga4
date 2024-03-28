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
        Schema::table('user_api_keys', function (Blueprint $table) {
            $table->json('enabled_events')->nullable();
            $table->boolean('webhook_status')->nullable();
            $table->string('webhook_id')->nullable();
            $table->string('ga4_property_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_api_keys', function (Blueprint $table) {
            //
        });
    }
};
