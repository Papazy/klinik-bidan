<?php

namespace App\Http\Controllers;

use App\Models\Dokter;
use App\Models\User;
use App\Models\Pasien;
use App\Models\Rekam;
use App\Models\Obat;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PDO;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
// require_once 'vendor/autoload.php';
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;
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
            dd($lookup);
            if (!$isValid) {
                throw new \Exception('Nomor telepon tidak valid', 21211);
            } 

            // Mendapatkan nomor antrian berikutnya
            $nomorAntrian = Rekam::whereDate('created_at', Carbon::today())->count() + 1;


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

            // Buat nomor antrian dan QR Code
            $unique_code = "$nomorAntrian" . Carbon::today()->format('dmy');
            $qrcode = QrCode::size(250)->generate("Nomor Antrian: $nomorAntrian\nNama: $request->Nama\nTanggal Daftar: " . Carbon::today()->format('d-m-Y') . "\nJam Daftar: " . Carbon::now()->format('H:i:s') . "\nUnique Code: $unique_code");

            // Simpan data rekam medis
            Rekam::create([
                'nomorantrian' => "00$nomorAntrian",
                'id_pasien' => $data->id,
                'keluhan' => $request->RekamMedis,
            ]);

            // Kirim pesan WhatsApp
            $message = $twilio_client->messages->create(
                "whatsapp:$client_number",
                [
                    "from" => "whatsapp:$twilio_whatsapp_number",
                    "body" => "Terima kasih telah mendaftar di Klinik Desita.\nNomor antrian Anda adalah *$nomorAntrian*\nNama: $request->Nama\nTanggal Daftar: " . Carbon::today()->format('d-m-Y') . "\nJam Daftar: " . Carbon::now()->format('H:i:s') . "\nLink *QR Code*: https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=$unique_code\n"
                ]
            );

            // Log hasil pengiriman
            Log::info("Message sent: " . $message->sid);

            // Kembalikan respons dengan data yang diperlukan
            return back()->with([
                'success' => 'Data berhasil ditambahkan',
                'nomorAntrian' => "00$nomorAntrian",
                'nama' => $request->Nama,
                'timestamps' => Carbon::now()->format('H:i:s'),
                'tanggaldaftar' => Carbon::today()->format('d-m-Y'),
                'qrcode' => $qrcode,
                'qrpath' => '', // Tidak perlu asset karena QR Code dihasilkan secara dinamis
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
        $data = Rekam::where('diagnosa', null)->get();
        return view('antrian-pasien', [
            'datarekam' => $data
        ]);
    }

    public function pasienlama()
    {
        $data = Dokter::all();
        return view('pasien-lama', [
            'dokter' => $data
        ]);
    }

    public function cekpasienlama(Request $request)
    {
        $validated = $request->validate(
            [
                "Nama" => 'required',
                "Lahir" => 'required',
            ]
        );

        $nama = $validated['Nama'];
        $lahir = $validated['Lahir'];

        $data = Pasien::where('nama', $nama)->where('lahir', $lahir)->get();

        if (count($data) > 0) {
            foreach ($data as $row) :
                return redirect('/pasien-lama')->with([
                    'success' => 'Data ditemukan',
                    'nama' => $row->nama,
                    'lahir' => $row->lahir->format('d - M(m) - Y'),
                    'alamat' => $row->alamat,
                    'kelamin' => $row->kelamin,
                    'id' => $row->id
                ]);
            endforeach;
        } else {
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
