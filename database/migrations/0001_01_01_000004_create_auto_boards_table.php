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
        Schema::create('auto_boards', function (Blueprint $table) {
            $table->id();
            // total information
            $table->decimal('total_collection_amount', 10, 2)->default(0); // total collection amount
            $table->integer('total_contributors')->default(0); // total contributors account
            $table->integer('total_distributed')->default(0); // total distributed account 
            // today information
            $table->decimal('today_collection_amount', 10, 2)->default(0); // today collection amount
            $table->integer('today_contributors')->default(0); // today contributors account
            $table->integer('today_distributed')->default(0); // today distributed in total amount  
            $table->integer('today_per_account_distributed')->default(0); // per account get today distributed amount       
      
            // distribution 
            $table->date('distribution_date')->unique(); // distribution date is the date of the distribution for evey day   
            $table->date('distributed_date')->nullable(); 

            // system information
            $table->enum('status', ['collection', 'distributed'])->default('collection'); // status of the auto board 
            $table->text('distribution_log')->nullable(); // log of the distribution process
            $table->timestamps();

            // index
            $table->index(['distribution_date' , 'status']);  // index for the distribution date and status
            $table->index('distributed_date'); // index for the distributed date
            $table->index('status'); // index for the status
            $table->index('created_at'); // index for the created at
        
            
           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auto_boards');
    }
};
