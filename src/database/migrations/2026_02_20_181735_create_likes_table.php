<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLikesTable extends Migration
{
    public function up(): void
    {
        Schema::create('likes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                    ->constrained()
                    ->onDelete('cascade');
                
            $table->foreignId('item_id')
                    ->constrained()
                    ->onDelete('cascade');
                
            $table->unique(['user_id', 'item_id']);

            $table->timestamps();
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('likes');
    }
}