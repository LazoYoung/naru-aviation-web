<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('flightplans', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId("booking_id")->constrained()->cascadeOnDelete();
            $table->foreignId("flight_id")->constrained()->cascadeOnDelete();
            $table->string("callsign", 10);
            $table->string("aircraft", 10);
            $table->string("origin", 4);
            $table->string("alternate", 4)->nullable();
            $table->string("destination", 4);
            $table->mediumInteger("altitude");
            $table->dateTime("off_block");
            $table->dateTime("on_block");
            $table->text("route")->nullable();
            $table->text("remarks")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('flightplans');
    }
};
