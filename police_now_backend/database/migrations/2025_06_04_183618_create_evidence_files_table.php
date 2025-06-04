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
        Schema::create('evidence_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('emergency_request_id')->constrained()->onDelete('cascade');
            $table->string('file_url');
            $table->string('file_type');
            $table->dateTime('timestamp');
            $table->float('latitude', 10, 7)->nullable();
            $table->float('longitude', 10, 7)->nullable();
            $table->boolean('is_verified')->default(false);
            $table->text('description')->nullable();
            $table->text('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evidence_files');
    }
};