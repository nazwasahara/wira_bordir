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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();

            $table->foreignId('material_id')->nullable();
            $table->foreignId('material_color_id')->nullable();
            $table->foreignId('sash_type_id')->nullable();
            $table->foreignId('font_id')->nullable();
            $table->foreignId('side_motif_id')->nullable();
            $table->foreignId('ribbon_color_id')->nullable();
            $table->foreignId('lace_option_id')->nullable();
            $table->foreignId('rombe_option_id')->nullable();
            $table->foreignId('motif_ribbon_option_id')->nullable();
            $table->foreignId('additional_item_option_id')->nullable();

            $table->integer('quantity');
            $table->decimal('final_price', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
