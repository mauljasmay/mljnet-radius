<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreign('customer_id')
                ->references('id')
                ->on('customers')
                ->cascadeOnDelete();
            
            $table->foreign('package_id')
                ->references('id')
                ->on('packages')
                ->nullOnDelete();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->foreign('invoice_id')
                ->references('id')
                ->on('invoices')
                ->cascadeOnDelete();
            
            $table->foreign('collector_id')
                ->references('id')
                ->on('collectors')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeignKey(['invoice_id']);
            $table->dropForeignKey(['collector_id']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeignKey(['customer_id']);
            $table->dropForeignKey(['package_id']);
        });
    }
};
