<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('expense_category_id')->constrained()->restrictOnDelete();
            $table->decimal('amount', 14, 2);
            $table->date('expense_date');
            $table->foreignId('property_id')->nullable()->constrained()->nullOnDelete();
            $table->string('description')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['expense_date', 'expense_category_id']);
            $table->index('property_id');
        });

        Schema::create('financial_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('type', 30);
            $table->decimal('amount', 14, 2);
            $table->date('transaction_date');
            $table->foreignId('property_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('description')->nullable();
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'transaction_date']);
            $table->index(['reference_type', 'reference_id']);
            $table->index(['property_id', 'customer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_transactions');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('expense_categories');
    }
};
