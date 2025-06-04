
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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->nullable()->constrained('user_roles');
            $table->string('username')->unique();
            $table->string('password');
            $table->string('email')->unique();
            $table->string('phone_number')->nullable();
            $table->string('full_name');
            $table->string('address')->nullable();
            $table->dateTime('registration_date');
            $table->boolean('is_verified')->default(false);
            $table->string('verification_status')->default('pending');
            $table->string('profile_image_url')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};