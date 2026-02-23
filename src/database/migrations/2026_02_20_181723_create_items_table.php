<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                    ->constrained()
                    ->onDelete('cascade');

            $table->foreignId('condition_id')
                    ->constrained()
                    ->onDelete('cascade');
            
            $table->string('name');
            $table->string('brand_name')->nullable();
            $table->text('description');
            $table->unsignedInteger('price');
            $table->string('image_path');
            $table->boolean('is_sold')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
}