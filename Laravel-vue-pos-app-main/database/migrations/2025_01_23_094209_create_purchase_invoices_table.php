<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('purchase_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_name', 250)->nullable();
            $table->decimal('total', 10, 2);
            $table->decimal('paid', 10, 2);
            $table->decimal('rest', 10, 2);
            $table->decimal('account_payable', 10, 2);

            $table->foreignId('user_id')->constrained()->onDelete('cascade')->onUpdate('restrict');
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade')->onUpdate('restrict');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_invoices');
    }
};
