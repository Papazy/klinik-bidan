<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Antrian extends Model
{
    use HasFactory;
    protected $fillable = [
        'no_antrian',
        'pasien_id',
        'jadwal_praktek',
        'jadwal_antrian',
        'tanggal_daftar_antrian',
    ];

    protected $dates = [
        'jadwal_antrian',
        'tanggal_daftar_antrian',
    ];

    // Relasi ke pasien
    public function pasien()
    {
        return $this->belongsTo(Pasien::class);
    }

   
    public function generateNoAntrian(){
        // Generate no antrian dengan maksimal 15 peserta perhari, 
        // bila lebih dari 15 peserta maka no antrian akan direset ke 1 dan diteruskan ke hari selanjutnya
        // mengembalikan nilai no antrian yang dihasilkan dan tanggal harinya dengan bentuk ['no_antrian' => '1', 'tanggal' => '2021-05-01']
        
        $waktu_sekarang = now();

        if ($waktu_sekarang->hour >= 18) {
            $waktu_sekarang = $waktu_sekarang->addDay()->startOfDay();
        }
    
        $tanggal = $waktu_sekarang->toDateString();
    
        $jumlah_antrian = Antrian::whereDate('tanggal_daftar_antrian', $tanggal)->count();
    
        $maksimal_antrian_per_hari = 15;
    
        if ($jumlah_antrian >= $maksimal_antrian_per_hari) {
            $tanggal = $waktu_sekarang->addDay()->toDateString();
            $nomor_antrian = 1;
        } else {
            $nomor_antrian = $jumlah_antrian + 1;
        }
    
        return ['no_antrian' => $nomor_antrian, 'tanggal' => $tanggal];

    }
}
