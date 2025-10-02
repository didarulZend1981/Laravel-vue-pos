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
        Schema::create('waste_products', function (Blueprint $table) {
            $table->id();
            $table->integer('waste_qty')->default(0);
            $table->string('purchase_price',50);
            $table->string('case',50)->nullable();
            $table->integer('refound')->default(0);
            $table->foreignId('purchase_invoice_id')->constrained()->onDelete('cascade')->onUpdate('restrict');
            $table->foreignId('product_id')->constrained()->onDelete('cascade')->onUpdate('restrict');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('waste_products');
    }
};
