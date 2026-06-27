<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = ['nik', 'name', 'address', 'phone', 'topsis_score', 'topsis_rank'];

    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }
}
