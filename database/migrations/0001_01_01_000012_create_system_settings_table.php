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
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // Setting key (e.g., 'sponsor_commission_rate', 'auto_income_eligibility')
            $table->text('value'); // Setting value (can be string, number, or JSON)
            $table->string('type')->default('string'); // Data type: string, integer, decimal, boolean, json
            $table->string('group')->default('general'); // Setting group: general, mlm, financial, auto_board, etc.
            $table->string('display_name'); // Human-readable name for the setting
            $table->text('description')->nullable(); // Description of what this setting controls
            $table->boolean('is_editable')->default(true); // Whether admins can edit this setting
            $table->boolean('is_public')->default(false); // Whether this setting is visible to users
            $table->json('options')->nullable(); // Available options for dropdown/select settings
            $table->string('validation_rules')->nullable(); // Laravel validation rules
            $table->timestamps();
            
            // Indexes for performance
            $table->index('group');
            $table->index(['group', 'is_editable']);
            $table->index('is_public');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
