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
        Schema::create('sub_accounts', function (Blueprint $table) {
            // Main Information
            $table->id();
            $table->string('name');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // user id is the id of the user
            $table->string('old_user_id')->nullable(); // old user id is the id of the old user
            $table->string('account_number')->unique();

            // MLM System
            $table->string('referral_code')->unique();
            $table->foreignId('active_package_id')->nullable()->constrained('packages')->onDelete('set null');
            $table->dateTime('active_package_purcased_at')->nullable();
            $table->foreignId('referral_by_id')->nullable()->constrained('sub_accounts')->onDelete('set null');
            $table->integer('direct_referral_count')->default(0);
            $table->integer('generation_count')->default(0); // generation count is the count of the generation of the sub account


          // financial information
          $table->decimal('total_balance', 10, 2)->default(0); // total deposit + total sponsor commission + total generation commission + total auto income - ( total withdrawal + total package purchase )
          $table->decimal('withdrawal_limit', 10, 2)->default(0); // total withdrawal only
          // debits
          $table->decimal('total_withdrawal', 10, 2)->default(0);
          $table->decimal('total_package_purchase', 10, 2)->default(0);
          // credits
          $table->decimal('total_deposit', 10, 2)->default(0); 
          $table->decimal('total_sponsor_commission', 10, 2)->default(0); 
          $table->decimal('total_generation_commission', 10, 2)->default(0); 
          $table->decimal('total_auto_income', 10, 2)->default(0);  






          


   


            // User Information
            $table->string('profile_picture')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->dateTime('last_balance_update_at')->nullable();
            $table->timestamps();

            // indexes for performance
            // will be added later
            
    
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_accounts');
    }
};
