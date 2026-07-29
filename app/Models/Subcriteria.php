<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subcriteria extends Model
{
    protected $table = 'subkriteria';

    protected $fillable = [
        'kriteria_id',
        'name',
        'score',
    ];

    public function criteria()
    {
        return $this->belongsTo(Criteria::class, 'kriteria_id');
    }
}
