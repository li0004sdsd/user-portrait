<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('export_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('exported_at')->useCurrent();
            $table->unsignedInteger('row_count');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('export_logs');
    }
};
