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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->uuid('siswa_id');
            $table->date('tanggal');
            $table->time('waktu_masuk')->nullable();
            $table->enum('status', ['hadir', 'terlambat', 'izin', 'sakit']);
            $table->text('keterangan')->nullable();
            $table->string('surat_izin')->nullable();
            $table->enum('method', ['qr_scan', 'manual'])->default('qr_scan');
            $table->uuid('created_by')->nullable();
            $table->timestamps();
            $table->foreign('siswa_id')->references('id')->on('siswas')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->unique(['siswa_id', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
