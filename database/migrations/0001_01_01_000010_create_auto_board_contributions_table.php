<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the database seeds.
     */
    public function up(): void
    {
        Schema::create('auto_board_contributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auto_board_id')->constrained('auto_boards')->onDelete('cascade');
            $table->foreignId('sub_account_id')->constrained('sub_accounts')->onDelete('cascade');
            $table->foreignId('package_purchase_id')->constrained('package_purchases')->onDelete('cascade');
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade'); // transaction id for detail of the transaction
            $table->decimal('amount', 10, 2);
            $table->text('notes')->nullable();

            // index with shorter names
            $table->index(['auto_board_id', 'sub_account_id', 'package_purchase_id'], 'abc_main_index');
            $table->index('sub_account_id');
            $table->index('created_at');
            $table->index('updated_at');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auto_board_contributions');
    }
};
