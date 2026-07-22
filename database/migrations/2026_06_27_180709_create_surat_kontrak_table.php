<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('surat_kontrak', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nasabah_id')->constrained('nasabah')->onDelete('cascade');
            $table->string('nomor_kontrak')->unique(); // nomor kontrak otomatis
            $table->enum('status', ['disetujui_admin', 'disetujui_cap', 'aktif', 'selesai'])->default('disetujui_admin');
            $table->date('tanggal_kontrak')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_kontrak');
    }
};
