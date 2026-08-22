<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'nasabah';
    protected $fillable = ['nik', 'name', 'address', 'phone', 'topsis_score', 'topsis_rank', 'status_persetujuan', 'nama_barang', 'nilai_barang', 'umur', 'kategori_nasabah', 'persentase_pinjaman'];

    public function assessments()
    {
        return $this->hasMany(Assessment::class, 'nasabah_id');
    }

    public function suratKontrak()
    {
        return $this->hasOne(SuratKontrak::class, 'nasabah_id');
    }
}
