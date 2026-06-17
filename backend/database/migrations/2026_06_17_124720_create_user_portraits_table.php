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
        Schema::create('user_portraits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('avatar')->nullable();
            $table->integer('age')->nullable();
            $table->string('gender')->nullable();
            $table->string('occupation')->nullable();
            $table->string('location')->nullable();
            $table->decimal('income_level', 10, 2)->nullable();
            $table->text('interests')->nullable();
            $table->text('pain_points')->nullable();
            $table->text('goals')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_portraits');
    }
};
