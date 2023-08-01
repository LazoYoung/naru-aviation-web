<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('flights', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId("user_id")->constrained()->cascadeOnDelete();
            $table->boolean("offline")->default(true);
            $table->smallInteger("status")->default(0);
            $table->time("off_block")->nullable();
            $table->time("on_block")->nullable();
            $table->double("latitude")->nullable();
            $table->double("longitude")->nullable();
            $table->integer("altitude")->nullable();
            $table->smallInteger("airspeed")->nullable();
            $table->smallInteger("heading")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('flights');
    }

};
