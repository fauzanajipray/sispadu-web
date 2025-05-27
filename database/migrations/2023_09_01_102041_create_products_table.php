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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('outlet_id');
            $table->string('name');
            $table->string('sku')->unique();
            $table->unsignedBigInteger('category_id');
            $table->integer('price');
            $table->string('photo', 512)->nullable();
            $table->timestamps();

            $table->foreign('outlet_id')
            ->references('id')
            ->on('outlets')
            ->onUpdate('cascade');

            $table->foreign('category_id')
            ->references('id')
            ->on('categories')
            ->onUpdate('cascade');

            $table->unique(['outlet_id','name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
