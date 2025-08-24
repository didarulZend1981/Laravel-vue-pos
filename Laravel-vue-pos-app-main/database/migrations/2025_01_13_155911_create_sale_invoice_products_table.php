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
        Schema::create('sale_invoice_products', function (Blueprint $table) {
            $table->id();
            $table->string('product_name')->nullable();
            $table->string('qty', 50);
            $table->string('rate',50);
            $table->string('sale_price', 50);
            $table->string('amount', 50);

            $table->foreignId('sale_invoice_id')->constrained()->onDelete('cascade')->onUpdate('restrict');
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('cascade')->onUpdate('restrict');
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->onUpdate('restrict');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_invoice_products');
    }
};
