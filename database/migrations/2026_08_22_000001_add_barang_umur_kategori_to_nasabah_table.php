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
        Schema::table('nasabah', function (Blueprint $table) {
            $table->string('nama_barang')->nullable();
            $table->double('nilai_barang')->nullable();
            $table->integer('umur')->nullable();
            $table->string('kategori_nasabah')->nullable();
            $table->integer('persentase_pinjaman')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nasabah', function (Blueprint $table) {
            $table->dropColumn(['nama_barang', 'nilai_barang', 'umur', 'kategori_nasabah', 'persentase_pinjaman']);
        });
    }
};
