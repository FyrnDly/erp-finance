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
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable()->unique();
            $table->string('name');
            $table->enum('type', ['asset', 'liability', 'equity', 'revenue', 'expense']);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable()->unique();
            $table->enum('status', ['pending','approved','rejected'])->default('pending');
            $table->decimal('amount', 15, 2)->default(0);
            $table->date('date');
            $table->date('approved_date')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('coa_id')->constrained('chart_of_accounts')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('submitted_by')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable()->unique();
            $table->string('subject');
            $table->date('issue_date');
            $table->date('due_date');
            $table->enum('status', ['unpaid','paid'])->default('unpaid');
            $table->foreignId('coa_id')->constrained('chart_of_accounts')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('submitted_by')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade')->onUpdate('cascade');
            $table->string('name');
            $table->integer('quantity');
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->timestamps();
        });
        
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable()->unique();
            $table->date('date');
            $table->text('description')->nullable();
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('expense_id')->nullable()->constrained('expenses')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->softDeletes();
        });
        
        Schema::create('journal_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entry_id')->constrained('journal_entries')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('coa_id')->constrained('chart_of_accounts')->onDelete('cascade')->onUpdate('cascade');
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_items');
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('chart_of_accounts');
    }
};
