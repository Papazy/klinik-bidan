<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pasien extends Model
{
    use HasFactory;
    protected $fillable = [
        'kodepasien',
        'nama',
        'alamat',
        'lahir',
        'nik',
        'kelamin',
        'telepon',
        'agama',
        'pendidikan',
        'pekerjaan'
    ];
    protected $guarded =['id'];

    protected $dates = ['lahir'];

    // public function dokters() {
    //     return $this->hasMany(Dokter::class);
    // }

    public function rekam(){
        return $this->hasMany(Rekam::class, 'id');
    }
    public function generateKode(){
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $kode_pasien = '';
        for($i = 0; $i < 6; $i++){
            $kode_pasien .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $kode_pasien;
    }

    public function generateKodePasien(){
        $kode_exist = true;
        $kode_pasien = '';
        while($kode_exist){
            $kode_pasien = $this->generateKode();
            $kode_exist = $this->where('kodepasien', $kode_pasien)->exists();
        }
        return $kode_pasien;
    }

    public function antrians()
    {
        return $this->hasMany(Antrian::class);
    }

}
