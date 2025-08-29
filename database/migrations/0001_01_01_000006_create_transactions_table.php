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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sub_account_id')->constrained('sub_accounts')->onDelete('cascade');
            $table->enum('type', [
                'deposit', 
                'withdrawal', 
                'package_purchase', 
                'sponsor_commission', 
                'generation_commission', 
                'auto_income', 
            ]);
            // amount and balance
            $table->decimal('amount', 10, 2);
            $table->decimal('balance_before', 10, 2)->default(0);
            $table->decimal('balance_after', 10, 2)->default(0);
            // checked by
            $table->foreignId('checked_by')->nullable()->constrained('admins')->onDelete('set null');
            $table->timestamp('checked_at')->nullable(); // checked at is the date and time when the transaction is checked
            $table->text('checked_notes')->nullable(); // checked notes is the notes when the transaction is checked by the admin
            
            // transaction details
            $table->text('description')->nullable();
            $table->json('metadata')->nullable(); // Additional transaction data
            
            // system information
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending'); // status of the transaction
            $table->string('system_ip')->nullable(); // system ip is the ip address of the system
            $table->string('system_user_agent')->nullable(); // system user agent is the user agent of the system





            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index(['sub_account_id', 'type']);
            $table->index(['sub_account_id', 'status']);
            $table->index(['type', 'status']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
