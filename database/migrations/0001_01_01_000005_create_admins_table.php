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
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable(); // email verified at is the date and time when the email is verified
            $table->string('password');
            $table->string('profile_picture')->nullable(); // profile picture is the profile picture of the admin
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active'); // status is the status of the admin
            $table->dateTime('last_login_at')->nullable(); // last login at is the date and time when the admin last logged in
            $table->string('last_login_ip')->nullable(); // last login ip is the ip address of the admin last login
            $table->string('last_login_user_agent')->nullable(); // last login user agent is the user agent of the admin last login
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            
            // index
            $table->index('status'); // index for the status
            $table->index('created_at'); // index for the created at
            $table->index('updated_at'); // index for the updated at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
