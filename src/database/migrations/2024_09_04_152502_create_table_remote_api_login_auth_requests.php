<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(Config::get('remote-api-login.table_name'), function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->string('code');
            $table->string('token', 64);
            $table->string('authenticatable_type')->nullable();
            $table->integer('authenticatable_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(Config::get('remote-api-login.table_name'));
    }
};
