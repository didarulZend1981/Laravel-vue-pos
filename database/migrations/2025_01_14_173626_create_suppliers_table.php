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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->string('email')->unique()->nullable();
            $table->string('phone')->unique();
            $table->string('address');
            $table->string('company_name')->nullable();
            $table->foreignId('brand_id')->nullable()->constrained()->onDelete('cascade')->onUpdate('restrict');
            $table->foreignId('user_id')-> constrained()->onDelete('cascade')->onUpdate('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
