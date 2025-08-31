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
        Schema::create('kyc_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            $table->string('nid_number', 17)->unique(); // Bangladeshi NID is 17 digits
            $table->string('nid_type')->default('smart_nid'); // smart_nid, old_nid, birth_certificate
            $table->string('full_name_bangla')->nullable(); // Name in Bengali
            $table->string('full_name_english');
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('blood_group')->nullable(); // A+, B+, O+, AB+, etc.
            
            $table->text('address');
            $table->string('postal_code')->nullable();
            
          
            $table->string('nid_front_image')->nullable();
            $table->string('nid_back_image')->nullable();
            
            
            $table->enum('status', [ 'kyc_pending','kyc_verified', 'kyc_failed'])->default('kyc_pending');  
            
            
            $table->foreignId('reviewed_by')->nullable()->constrained('admins')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            
           
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['user_id', 'status']);
            $table->index(['nid_number']);
            $table->index(['status']);
            $table->index('created_at');
            $table->index('reviewed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kyc_verifications');
    }
};
