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
        Schema::create('auto_board_distributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auto_board_id')->constrained('auto_boards')->onDelete('cascade');
            $table->foreignId('sub_account_id')->constrained('sub_accounts')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->integer('direct_referral_count')->default(0); // from sub account direct referral count  when the auto board is distributed
            $table->text('notes')->nullable();
            $table->timestamps();
          

            // index with shorter names
            $table->index(['auto_board_id', 'sub_account_id', 'direct_referral_count'], 'abd_main_index');
            $table->index('sub_account_id');
            $table->index('created_at');
            $table->index('updated_at');
        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auto_board_distributions');
    }
};
