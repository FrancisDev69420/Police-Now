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
        Schema::create('officers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('badge_number')->unique();
            $table->string('rank');
            $table->string('department');
            $table->string('status')->default('active');
            $table->dateTime('service_start_date');
            $table->dateTime('shift_start')->nullable();
            $table->dateTime('shift_end')->nullable();
            $table->string('specialization')->nullable();
            $table->boolean('on_duty')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('officers');
    }
};