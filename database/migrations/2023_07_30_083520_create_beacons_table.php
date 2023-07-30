<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('beacons', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId("flight_id")->constrained()->cascadeOnDelete();
            $table->smallInteger("status")->default(0);
            $table->double("latitude");
            $table->double("longitude");
            $table->integer("altitude");
            $table->smallInteger("airspeed");
            $table->smallInteger("heading");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('beacons');
    }
};
