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
        Schema::create('kelas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tingkat_kelas_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('jurusan_id')->constrained()->onDelete('cascade');
            // $table->string('rombel', 5); // 1, 2, 3, dst
            $table->string('nama_kelas', 20); // X IPA 1, XI IPS 2, dst
            // $table->integer('kapasitas_maksimal')->default(36);
            $table->string('wali_kelas')->nullable();
            // $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
            $table->unique(['tingkat_kelas_id', 'jurusan_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
