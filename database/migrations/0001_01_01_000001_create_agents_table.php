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
        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // name of the agent
            $table->string('phone')->unique(); // phone of the agent
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable(); // email verified at is the date and time when the email is verified
            $table->string('password'); // password of the agent
            $table->string('address')->nullable(); // address of the agent
            $table->string('profile_picture')->nullable(); // profile picture of the agent
            $table->string('agent_account_number')->unique(); // account number of the agent


            // financial information
            $table->decimal('total_balance', 10, 2)->default(0); // total balance of the agent
            $table->decimal('total_debit', 10, 2)->default(0); // total debit of the agent
            $table->decimal('total_credit', 10, 2)->default(0); // total credit of the agent
            $table->decimal('safety_balance', 10, 2)->default(0); // fixed reserved amount for safety

            // login information log for the agent
            $table->dateTime('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->string('last_login_user_agent')->nullable();

            // status of the agent
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->index('agent_account_number');
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
        Schema::dropIfExists('agents');
    }
};
