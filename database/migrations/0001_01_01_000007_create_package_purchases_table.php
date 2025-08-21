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
        Schema::create('package_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sub_account_id')->constrained('sub_accounts')->onDelete('cascade');
            $table->foreignId('package_id')->constrained('packages')->onDelete('cascade');
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade'); // transaction id for detail of the transaction
            $table->decimal('amount', 10, 2);
            $table->text('notes')->nullable();

            // index
            $table->index(['sub_account_id', 'package_id' , 'transaction_id']); // index for the sub account id and package id and transaction id
            $table->index('sub_account_id'); // index for the sub account id
            $table->index('package_id'); // index for the package id
            $table->index('transaction_id'); // index for the transaction id
            $table->index('created_at'); // index for the created at
            $table->index('updated_at'); // index for the updated at

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_purchases');
    }
};
