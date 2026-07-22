<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    protected $table = 'penilaian';
    protected $fillable = ['nasabah_id', 'kriteria_id', 'value'];

    // Override default FK names
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'nasabah_id');
    }

    public function criteria()
    {
        return $this->belongsTo(Criteria::class, 'kriteria_id');
    }
}
