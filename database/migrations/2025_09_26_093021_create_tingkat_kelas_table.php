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
        Schema::create('tingkat_kelas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('tingkat', 10); // X, XI, XII
            // $table->string('nama_tingkat', 50); // Kelas 10, Kelas 11, Kelas 12
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tingkat_kelas');
    }
};
