<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('category');
            $table->decimal('unit_price', 10, 2);
            $table->text('description');
            $table->enum('stock_status', ['in_stock', 'out_of_stock'])->index();
            $table->timestamps();

            $table->index(['category', 'stock_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plants');
    }
};
