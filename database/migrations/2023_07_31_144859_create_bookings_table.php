<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId("user_id")->constrained()->cascadeOnDelete();
            $table->timestamp("preflight_at");
            $table->string("callsign", 10);
            $table->string("aircraft", 10);
            $table->string("origin", 4);
            $table->string("alternate", 4)->nullable();
            $table->string("destination", 4);
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
        Schema::dropIfExists('bookings');
    }
};
