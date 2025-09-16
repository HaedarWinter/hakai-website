<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tugas extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'tugas',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',   // <--- tambahin ini
    ];

    protected $dates = [
        'tanggal_mulai',
        'tanggal_selesai',
        'deleted_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
