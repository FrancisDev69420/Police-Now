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
        Schema::create('emergency_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resident_id')->constrained()->onDelete('cascade');
            $table->string('emergency_type');
            $table->string('status')->default('pending');
            $table->dateTime('request_time');
            $table->dateTime('response_time')->nullable();
            $table->float('request_latitude', 10, 7);
            $table->float('request_longitude', 10, 7);
            $table->text('location_details')->nullable();
            $table->string('priority_level')->default('medium');
            $table->text('additional_notes')->nullable();
            $table->boolean('is_resolved')->default(false);
            $table->dateTime('resolution_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emergency_requests');
    }
};