<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

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


    public function generateNoAntrian()
    {
        // Generate no antrian dengan maksimal 15 peserta perhari, 
        // bila lebih dari 15 peserta maka no antrian akan direset ke 1 dan diteruskan ke hari selanjutnya
        // mengembalikan nilai no antrian yang dihasilkan dan tanggal harinya dengan bentuk ['no_antrian' => '1', 'tanggal' => '2021-05-01']

        $waktu_sekarang = now();

        if ($waktu_sekarang->hour >= 18) {
            $waktu_sekarang = $waktu_sekarang->addDay()->startOfDay();
        }

        $tanggal = $waktu_sekarang->toDateString();

        $jumlah_antrian = Antrian::whereDate('jadwal_antrian', $tanggal)->count();

        $maksimal_antrian_per_hari = 15;
        if ($jumlah_antrian >= $maksimal_antrian_per_hari) {

            while ($jumlah_antrian >= $maksimal_antrian_per_hari) {
                $waktu_sekarang = $waktu_sekarang->addDay();
                $tanggal = $waktu_sekarang->toDateString();
                $jumlah_antrian = Antrian::whereDate('jadwal_antrian', $tanggal)->count();
            }
            $nomor_antrian = $jumlah_antrian + 1;
        } else {
            $nomor_antrian = $jumlah_antrian + 1;
        }

        // dd($waktu_sekarang);
        // dd($tanggal);
        // dd($jumlah_antrian);
        return ['no_antrian' => $nomor_antrian, 'tanggal' => $tanggal];
    }

    public static function generateQrCode($nomorAntrian, $nama, $unique_code)
    {
        try{

            $qrcode = QrCode::format('png')
            ->size(300)
            ->generate("Nomor Antrian: $nomorAntrian\nNama: $nama\nTanggal Daftar: " . Carbon::today()->format('d-m-Y') . "\nJam Daftar: " . Carbon::now()->format('H:i:s') . "\nUnique Code: $unique_code");
            
            $output_file = '/img/qr-code/img-' . $unique_code . '.png';
            Storage::disk('public')->put($output_file, $qrcode); //storage/app/public/img/qr-code/img-1557309130.png
        }catch(\Exception $e){
            dd($e);
        }

        return $output_file;
    }
}
