<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratKontrak extends Model
{
    use HasFactory;

    protected $table = 'surat_kontrak';
    protected $fillable = ['nasabah_id', 'nomor_kontrak', 'status', 'tanggal_kontrak', 'keterangan'];

    public function nasabah()
    {
        return $this->belongsTo(Customer::class, 'nasabah_id');
    }
}
