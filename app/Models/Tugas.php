<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tugas extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tugas',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'tugas_file', // File dari admin
        'karyawan_file', // File dari karyawan
        'catatan' // Catatan jika ditolak
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed(); // Jika menggunakan soft deletes
    }
}
