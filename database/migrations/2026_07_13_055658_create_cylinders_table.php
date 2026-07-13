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
        Schema::create('cylinders', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // e.g., 'Domestic (14.2kg)', 'Commercial (19kg)'
            $table->integer('full_stock')->default(0); // Sealed ready-for-sale units
            $table->integer('empty_stock')->default(0); // Collected cylinders needing refills
            $table->decimal('price', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cylinders');
    }
};
