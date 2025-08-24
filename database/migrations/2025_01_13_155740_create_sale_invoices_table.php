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
        Schema::create('sale_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_name', 250)->nullable();
            $table->string('total',50);
            $table->string('discount',50)->nullable();
            $table->string('vat',50)->nullable();
            $table->string('subtotal',50)->nullable();
            $table->string('paid',50)->nullable();
            $table->string('rest',50)->nullable();
            $table->string('customer_payable',50)->nullable();

            $table->foreignId('user_id')->constrained()->onDelete('cascade')->onUpdate('restrict');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade')->onUpdate('restrict');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_invoices');
    }
};
