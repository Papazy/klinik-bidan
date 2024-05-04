<?php

namespace App\Http\Controllers;

use App\Models\Antrian;
use App\Models\Dokter;
use App\Models\Jadwal;
use App\Models\Pasien;
use App\Models\Rekam;
use App\Models\Obat;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PDO;
// require_once 'vendor/autoload.php';
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;



class PasienController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $datapasien = Pasien::get();


        return view('pasien', compact('datapasien'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pasien-form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            // Inisialisasi klien Twilio dan data Twilio
            $sid = "AC01dae14034bd9fcbf4d4bc2a2ea30887";
            $token = "64d28bb60c6949fcceada85553157360";
            $twilio_whatsapp_number = "+14155238886";
            $twilio_client = new Client($sid, $token);

            // Validasi data input
            $this->validate($request, [
                'Nama' => 'required',
                'Alamat' => 'required',
                'Lahir' => 'required',
                'NIK' => 'required',
                'Kelamin' => 'required',
                'Telepon' => 'required|numeric', // Memastikan telepon berupa angka
                'Agama' => 'required',
                'Pekerjaan' => 'required',
                'RekamMedis' => 'required',
                'jadwal' => 'required'
            ]);

            // Format nomor telepon
            $client_number = $request->Telepon;
            if (Str::startsWith($client_number, '08')) {
                $client_number = '+62' . Str::substr($client_number, 1);
            } elseif (Str::startsWith($client_number, '+62')) {
                $client_number = '' . $client_number;
            } elseif (Str::startsWith($client_number, '62')) {
                $client_number = '+62' . Str::substr($client_number, 2);
            } else {
                throw new \Exception('Nomor telepon tidak valid', 21211);
            }

            $lookup = $twilio_client->lookups->v1->phoneNumbers($client_number)->fetch();

            // Dapatkan hasil lookup
            $isValid = $lookup->phoneNumber;
            // dd($lookup);
            if (!$isValid) {
                throw new \Exception('Nomor telepon tidak valid', 21211);
            }


            // Simpan data pasien atau temukan data pasien yang sudah ada
            $data = Pasien::firstOrCreate([
                'nama' => ucwords(strtolower($request->Nama)),
                'alamat' => $request->Alamat,
                'lahir' => $request->Lahir,
                'nik' => $request->NIK,
                'kelamin' => $request->Kelamin,
                'telepon' => $client_number,
                'agama' => $request->Agama,
                'pekerjaan' => $request->Pekerjaan
            ]);

            // Periksa apakah data tersebut sudah memiliki kodepasien
            if (!$data->kodepasien) {
                // Jika belum, buat kode pasien baru
                $kode_pasien = $data->generateKodePasien();
                $data->kodepasien = $kode_pasien;
                $data->save();
            }
            // Mendapatkan nomor antrian berikutnya
            $obj_antrian = new Antrian();
            $resAntrian = $obj_antrian->generateNoAntrian();
            $nomorAntrian = $resAntrian["no_antrian"];
            $jadwalAntrian = $resAntrian["tanggal"];

            // Buat nomor antrian dan QR Code
            $data_antrian = Antrian::create([
                'no_antrian' => $nomorAntrian,
                'pasien_id' => $data->kode_pasien,
                'jadwal_praktek' => $request->jadwal,
                'jadwal_antrian' => $jadwalAntrian,
                'tanggal_daftar_antrian' => Carbon::today()
            ]);

            $unique_code = $data_antrian->id . "-" . $data->kodepasien;
            // Generate Qr Code
            $output_file = Antrian::generateQrCode($nomorAntrian, $data->nama, $unique_code);
            // Kirim pesan WhatsApp
            $message = Pasien::kirimPesanWhatsApp($client_number, $data->kodepasien, $data_antrian->jadwal_antrian, $data_antrian->jadwal_praktek, $nomorAntrian, $data->nama, $unique_code);




            // Simpan data rekam medis
            Rekam::create([
                'id_antrian' => $data_antrian->id,
                'id_pasien' => $data->id,
                'keluhan' => $request->RekamMedis,
            ]);


            // Log hasil pengiriman
            Log::info("Message sent: " . $message->sid);
            // dd($message);

            // Kembalikan respons dengan data yang diperlukan
            return back()->with([
                'success' => 'Data berhasil ditambahkan',
                'nomorAntrian' => "$nomorAntrian",
                'kodepasien' => $data->kodepasien,
                'nama' => $request->Nama,
                'timestamps' => Carbon::now()->format('H:i:s'),
                'tanggaldaftar' => Carbon::today()->format('d-m-Y'),
                'jadwalAntrian' => Carbon::parse($jadwalAntrian)->format('d-m-Y'),
                'jadwalPraktik' => $request->jadwal,
                'qrpath' => asset("storage" . $output_file), // Tidak perlu asset karena QR Code dihasilkan secara dinamis
                "message" => "Message sent: " . $message->sid
            ]);
        } catch (\Exception $e) {
            // dd($e->getMessage(), $e->getCode());
            if ($e->getCode() == 21211 || $e->getCode() == 20404) {
                return back()->with(['error' => 'Maaf, nomor telepon tidak valid']);
            }
            return back()->with(['error' => 'Data gagal ditambahkan: ' . $e->getMessage()]);
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Pasien  $pasien
     * @return \Illuminate\Http\Response
     */
    public function show(Pasien $pasien)
    {
        $pasien = Pasien::where('id', $pasien)->get();
        return view('pasien', compact('pasien'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Pasien  $pasien
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $pasien = Pasien::findOrfail($id);
        $rekam = Rekam::where('id_pasien', $id)->whereNotNull('diagnosa')->get();

        return view('pasien-rekammedis', [
            'pasien' => $pasien,
            'rekam' => $rekam,
            'dokter' => Dokter::all(),
            'obat' => Obat::all()
        ]);
    }

    // public function ubah($id)
    // {
    //     $pasien = Pasien::findOrfail($id);
    //     return view('pasien-form-edit', compact('pasien-rekammedis'));
    // }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Pasien  $pasien
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // dd($request);
        $request->validate([
            'Kodepasien' => 'required',
            'Nama' => 'required',
            'Alamat' => 'required',
            'Lahir' => 'required',
            'NIK' => 'required',
            'Kelamin' => 'required',
            'Telepon' => 'required',
            'Agama' => 'required',
            // 'Pendidikan' => 'required',
            'Pekerjaan' => 'required'
        ]);

        $pasien = Pasien::find($id);

        $pasien->update([
            'kodepasien' => $request->Kodepasien,
            'nama' => $request->Nama,
            'alamat' => $request->Alamat,
            'lahir' => $request->Lahir,
            'nIK' => $request->NIK,
            'kelamin' => $request->Kelamin,
            'telepon' => $request->Telepon,
            'agama' => $request->Agama,
            // // 'pendidikan' => $request->Pendidikan,
            'pekerjaan' => $request->Pekerjaan
        ]);

        return redirect()->route('pasien.index')->with('success', 'Data telah diubah');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Pasien  $pasien
     * @return \Illuminate\Http\Response
     */
    public function destroy(Pasien $pasien)
    {
        $pasien->delete();
        $rekam = Rekam::where('id_pasien', $pasien->id);
        $rekam->delete();
        return redirect('/pasien')->with('success', 'Data terhapus');
    }

    public function antrianpasien()
    {
        $data = Antrian::all();
        $dates = Antrian::whereDate('jadwal_antrian', '>=', now())
                ->distinct()
                ->pluck('jadwal_antrian')
                ->map(function ($date) {
                    return \Carbon\Carbon::parse($date)->toDateString();
                });
        return view('antrian-pasien', [
            'datarekam' => $data,
            'dates' => $dates 
        ]);
    }

    public function pasienlama()
    {
        $data = Dokter::all();
        $jadwal = Jadwal::all();
        return view('pasien-lama', [
            'dokter' => $data,
            'jadwal' => $jadwal
        ]);
    }

    public function cekpasienlama(Request $request)
    {
        $q = $request->q;

        // Validasi input berdasarkan nilai 'q'
        $validated = [];
        if ($q == "nama") {
            $validated = $request->validate([
                "nama" => 'required',
            ]);
        } elseif ($q == "kodepasien") {
            $validated = $request->validate([
                "kodepasien" => 'required',
            ]);
        }

        // Lakukan pencarian berdasarkan nilai 'q' yang sudah divalidasi
        $data = Pasien::where($q, $validated[$q])->get();
        $jadwal = Jadwal::all();
        if ($data->isNotEmpty()) {
            // Jika data ditemukan, redirect ke halaman pasien-lama dengan data yang ditemukan
            foreach ($data as $row) {
                return redirect('/pasien-lama')->with([
                    'success' => 'Data ditemukan',
                    'nama' => $row->nama,
                    'lahir' => $row->lahir->format('d - M(m) - Y'),
                    'alamat' => $row->alamat,
                    'kelamin' => $row->kelamin,
                    'id' => $row->id,
                    'jadwal' => $jadwal
                ]);
            }
        } else {
            // Jika data tidak ditemukan, redirect ke halaman pasien-lama dengan pesan gagal
            return redirect('/pasien-lama')->with([
                'failed' => 'Data tidak ditemukan'
            ]);
        }
    }


    public function rekamstore(Request $request)
    {
        $validated = $request->validate([
            'idpasien' => 'required',
            // 'layanan' => 'required',
            'keluhan' => 'required',
            'dokter' => 'required',
            'diagnosa' => 'required',
            'obat' => 'required',
            'jumlahobat' => 'required',
            'keterangan' => 'required'
        ]);

        Rekam::create([
            'jumlahobat' => $validated['jumlahobat'],
            'id_pasien' => $validated['idpasien'],
            'nomorantrian' => 0,
            // // 'layanan' => $validated['layanan'],
            'keluhan' => $validated['keluhan'],
            'id_dokter' => $validated['dokter'],
            'diagnosa' => $validated['diagnosa'],
            'id_obat' => $validated['obat'],
            'keterangan' => $validated['keterangan']
        ]);

        $obat = Obat::find($validated['obat']);
        $obat->stok = $obat->stok - $validated['jumlahobat'];
        $obat->save();

        return back()->with('success', 'Data berhasil ditambahkan');
    }

    public function updatepasien(Request $request)
    {
        $validated = $request->validate([
            'idpasien' => 'required',
            'Kodepasien' => 'required',
            'Nama' => 'required',
            'Alamat' => 'required',
            'Lahir' => 'required',
            'NIK' => 'required',
            'Kelamin' => 'required',
            'Telepon' => 'required',
            'Agama' => 'required',
            // 'Pendidikan' => 'required',
            'Pekerjaan' => 'required'
        ]);

        $pasien = Pasien::find($validated['idpasien']);
        $pasien->kodepasien = $validated['Kodepasien'];
        $pasien->nama = $validated['Nama'];
        $pasien->alamat = $validated['Alamat'];
        $pasien->lahir = $validated['Lahir'];
        $pasien->nik = $validated['NIK'];
        $pasien->kelamin = $validated['Kelamin'];
        $pasien->telepon = $validated['Telepon'];
        $pasien->agama = $validated['Agama'];
        // // $pasien->pendidikan = $validated['Pendidikan'];
        $pasien->pekerjaan = $validated['Pekerjaan'];
        $pasien->save();

        return back()->with('success', 'Data Terupdate');
    }
}
