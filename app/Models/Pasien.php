<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Twilio\Rest\Client;
use Carbon\Carbon;


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
        'pekerjaan',
        'catatan'
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

    public static function kirimPesanWhatsApp($client_number, $kode_pasien, $jadwal_antrian, $jadwal_praktek,  $nomorAntrian, $nama, $unique_code ){
          // Inisialisasi klien Twilio dan data Twilio
          $sid = "AC01dae14034bd9fcbf4d4bc2a2ea30887";
          $token = "64d28bb60c6949fcceada85553157360";
          $twilio_whatsapp_number = "+14155238886";
          $twilio_client = new Client($sid, $token);

        $message = $twilio_client->messages->create(
            "whatsapp:$client_number",
            [
                "from" => "whatsapp:$twilio_whatsapp_number",
                "body" => "Terima kasih telah mendaftar di Klinik Desita.\nNomor antrian Anda adalah *$nomorAntrian*\nNama: $nama\nKode Pasien: ".$kode_pasien."\nJadwal: " . Carbon::parse($jadwal_antrian)->format('d-m-Y') . "\nJam: " . $jadwal_praktek . "\nLink *QR Code*: https://api.qrserver.com/v1/create-qr-code/?size=400x400&data=$unique_code&margin=50\n"
            ]
        );
        return $message;
    }

}
