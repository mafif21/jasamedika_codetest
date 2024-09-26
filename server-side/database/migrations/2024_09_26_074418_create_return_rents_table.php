<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('return_rents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rent_id')->constrained()->onDelete('cascade');
            $table->string('plate_number');
            $table->integer('total_days');
            $table->decimal('total_cost', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('return_rents');
    }
};
