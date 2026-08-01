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
        Schema::table('transactions', function (Blueprint $table) {
            $table->decimal('unit_price', 10, 2)->default(0)->after('qty');
            $table->decimal('discount_percent', 5, 2)->default(0)->after('unit_price');
            $table->decimal('taxable_amount', 10, 2)->default(0)->after('discount_percent');
            $table->decimal('tax_percent', 5, 2)->default(0)->after('taxable_amount');
            $table->decimal('tax_amount', 10, 2)->default(0)->after('tax_percent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['unit_price', 'discount_percent', 'taxable_amount', 'tax_percent', 'tax_amount']);
        });
    }
};
