<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Criteria extends Model
{
    use HasFactory;

    protected $table = 'kriteria';
    protected $fillable = ['code', 'name', 'type', 'weight'];

    public function assessments()
    {
        return $this->hasMany(Assessment::class, 'kriteria_id');
    }
}
