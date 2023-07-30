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
            $table->boolean("cancelled")->default(false);
            $table->string("aircraft", 10);
            $table->string("flight_number", 10);
            $table->dateTime("sched_off_block");
            $table->dateTime("sched_on_block");
            $table->string("origin", 4);
            $table->string("alternate", 4)->nullable();
            $table->string("destination", 4);
            $table->mediumInteger("altitude");
            $table->text("route")->nullable();
            $table->text("remarks")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('flights');
    }

};
