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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('category_id')->constrained('ticket_categories');
            $table->text('message');
            $table->text('attachments')->nullable(); // file path for attachments like images.
            $table->text('reply')->nullable();
            $table->enum('status', ['open', 'closed']); // open means the ticket is open, closed means the ticket is closed
            $table->foreignId('reviewed_by')->nullable()->constrained('admins')->onDelete('set null');


            // timestamp
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // index
            $table->index('user_id');
            $table->index(['user_id', 'status']);
            $table->index('reviewed_by');
            $table->index(['reviewed_by', 'status']);
            $table->index('category_id');
            $table->index('reviewed_at');
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
