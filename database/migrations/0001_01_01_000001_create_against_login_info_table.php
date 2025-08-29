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
        Schema::create('against_users', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // name of the against login info   // name of the against user
            $table->string('phone')->unique(); // phone of the against login info   // phone of the against user
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable(); // email verified at is the date and time when the email is verified
            $table->string('password'); // password of the against login info
            $table->string('address')->nullable(); // address of the against login info
            $table->string('profile_picture')->nullable(); // profile picture of the against login info
            $table->string('against_account_number')->unique(); // account number of the against login info


            // financial information
            $table->decimal('total_balance', 10, 2)->default(0); // total balance of the against user
            $table->decimal('total_debit', 10, 2)->default(0); // total debit of the against user
            $table->decimal('total_credit', 10, 2)->default(0); // total credit of the against user
            $table->decimal('safety_balance', 10, 2)->default(0); // fixed reserved amount for safety

            // login information log for the against user
            $table->dateTime('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->string('last_login_user_agent')->nullable();

            // status of the against user
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->index('against_account_number');
            $table->index('phone');
            $table->index('email');
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('against_users');
    }
};
