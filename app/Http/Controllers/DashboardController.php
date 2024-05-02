<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Antrian;
use App\Models\Dokter;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Rekam;
use App\Models\Obat;
use App\Models\Pasien;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
// require_once 'vendor/autoload.php';
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;



class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function pendaftaran()
    {
        $data = Dokter::all();
        return view('pendaftaran', [
            'dokter' => $data
        ]);
    }

    public function antrianpasien()
    {
        $data = Antrian::all();
        // dd($data[0]->pasien);
        return view('antrian-pasien-admin', [
            'datarekam' => $data
        ]);
    }

    public function tambahantrianpasien(Request $request)
    {
        {
            try {
                // Inisialisasi klien Twilio dan data Twilio
                $sid = "AC01dae14034bd9fcbf4d4bc2a2ea30887";
                $token = "64d28bb60c6949fcceada85553157360";
                $twilio_whatsapp_number = "+14155238886";
                $twilio_client = new Client($sid, $token);
    
                // Validasi data input
                $this->validate($request, [
                   'user_id' => 'required',
                    'jadwal' => 'required'
                ]);

    
                // Simpan data pasien atau temukan data pasien yang sudah ada
                $data = Pasien::where('kodepasien', $request->user_id)->first();
                $client_number = $data->telepon;
               
                // Mendapatkan nomor antrian berikutnya
                $obj_antrian = new Antrian();
                $resAntrian = $obj_antrian->generateNoAntrian();
                $nomorAntrian = $resAntrian["no_antrian"];
                $jadwalAntrian = $resAntrian["tanggal"];
    
                // Buat nomor antrian dan QR Code
                $data_antrian = Antrian::create([
                    'no_antrian' => $nomorAntrian,
                    'pasien_id' => $data->id,
                    'jadwal_praktek' => $request->jadwal,
                    'jadwal_antrian' => $jadwalAntrian,
                    'tanggal_daftar_antrian' => Carbon::today()
                ]);
    
                $unique_code = "$nomorAntrian" . Carbon::today()->format('dmy');
                $qrcode = QrCode::format('png')
                    ->size(300)
                    ->generate("Nomor Antrian: $nomorAntrian\nNama: $request->Nama\nTanggal Daftar: " . Carbon::today()->format('d-m-Y') . "\nJam Daftar: " . Carbon::now()->format('H:i:s') . "\nUnique Code: $unique_code");
    
                $output_file = '/img/qr-code/img-' . $unique_code . '.png';
                Storage::disk('public')->put($output_file, $qrcode); //storage/app/public/img/qr-code/img-1557309130.png
    
                // Simpan data rekam medis
                Rekam::create([
                    'id_antrian' => $data_antrian->id,
                    'id_pasien' => $data->id,
                    'keluhan' => "-",
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
                // dd($message);
    
                // Kembalikan respons dengan data yang diperlukan
                return back()->with([
                    'successAddAntrian' => 'Data berhasil ditambahkan',
                    'nomorAntrian' => "$nomorAntrian",
                    'kodepasien' => $data->kodepasien,
                    'nama' => $data->nama,
                    'timestamps' => Carbon::now()->format('H:i:s'),
                    'tanggaldaftar' => Carbon::today()->format('d-m-Y'),
                    'jadwalAntrian' => Carbon::parse($jadwalAntrian)->format('d-m-Y'),
                    'jadwalPraktik' => $request->jadwal,
                    'qrcode' => $qrcode,
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
    }
    public function cekpasienlama(Request $request)
    {
        $validated = $request->validate([
            "Nama" => 'required',

        ]);

        $nama = $validated['Nama'];


        $data = DB::table('pasiens')->where('nama', $nama)->get();

        if (count($data) > 0) {
            foreach ($data as $row) :
                return redirect('/pendaftaran')->with([
                    'success' => 'Data ditemukan',
                    'nama' => $row->nama,
                    'lahir' => $row->lahir,
                    'alamat' => $row->alamat,
                    'kelamin' => $row->kelamin,
                    'id' => $row->id
                ]);
            endforeach;
        } else {
            return redirect('/pendaftaran')->with([
                'failed' => 'Data tidak ditemukan'
            ]);
        }
    }

    public function addrekam(Request $request)
    {
        $validate = $request->validate([
            'id_player' => 'required',
            // 'layanan' => 'required',
            'keluhan' => 'required',
            'dokter' => 'required'
        ]);

        $nomorAntrian = 1;
        $cekData = Rekam::whereDate('created_at', Carbon::today())->latest()->first();
        if ($cekData) {
            $nomorAntrian = $cekData->nomorantrian + 1;
        }

        $Rekam = Rekam::create([
            'nomorantrian' => "00" . $nomorAntrian,
            'id_pasien' => $validate['id_player'],
            // // 'layanan' => $validate['layanan'],
            'keluhan' => $validate['keluhan'],
            'id_dokter' => $validate['dokter']
        ]);

        $latestrekam = Rekam::all()->last();
        $pasienid = $latestrekam->id_pasien;
        $pasientable = DB::table('pasiens')->where('id', $pasienid)->get();

        foreach ($pasientable as $row) :

            return redirect('pendaftaran')->with([
                'addsuccess' => 'Data berhasil ditambahkan'
            ]);

        endforeach;
    }

    public function diagnosa()
    {
        $data = Rekam::all();
        return view('diagnosa', [
            'data' => $data
        ]);
    }

    public function diagnosaform($id)
    {
        $data = DB::table('rekams')->where('id', $id)->get();
        return view('diagnosa-form', [
            'data' => $data
        ]);
    }

    public function tambahpasienform()
    {
        $data = Dokter::all();
        return view('tambahpasienform', [
            'dokter' => $data
        ]);
    }

    public function tambahpasien(Request $request)
    {
        $sid = "AC01dae14034bd9fcbf4d4bc2a2ea30887";
        $token = "64d28bb60c6949fcceada85553157360";
        $twilio_client = new Client($sid, $token);

        try {
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
            if ($request->has('Catatan')) {
                $data->catatan = $request->Catatan;
                $data->save();
            }

            $jadwal = Jadwal::all();
            return back()->with([
                'successAddUser' => 'Data berhasil ditambahkan',
                'kodepasien' => $data->kodepasien,
                'nama' => $request->Nama,
                'timestamps' => Carbon::now()->format('H:i:s'),
                'tanggaldaftar' => Carbon::today()->format('d-m-Y'),
                'jadwal' => $jadwal
            ]);
        } catch (\Exception $e) {
            // dd($e->getMessage(), $e->getCode());
            if ($e->getCode() == 21211 || $e->getCode() == 20404) {
                return back()->with(['error' => 'Maaf, nomor telepon tidak valid']);
            }
            return back()->with(['error' => 'Data gagal ditambahkan: ' . $e->getMessage()]);
        }
    }

    public function deleteantrianadmin($id)
    {
        $rekam = Rekam::find($id);
        $rekam->delete();
        return view('antrian-pasien-admin');
    }

    public function editrekam($od, $id)
    {
        return view('edit-rekam-admin-form', [
            'rekam' => Rekam::find($id),
            'obat' => Obat::all(),
            'dokter' => Dokter::all(),
            'id_pasien' => $od
        ]);
    }

    public function updaterekam(Request $request)
    {
        $validated = $request->validate([
            'idrekam' => 'required',
            // 'layanan' => 'required',
            'keluhan' => 'required',
            'dokter' => 'required',
            'diagnosa' => 'required',
            'idpasien' => 'required',
            // 'obat' => '',
            // 'jumlahobat' => '',
            // 'keterangan' => '',
            // 'Ruang' => '',
            // 'Darah' => '',
            // 'Tinggi' => '',
            // 'Berat' => '',
            // 'LingkarBadan' => ''
        ]);


        $rekam = Rekam::find($validated['idrekam']);

        if ($request->obat != '' && $request->jumlahobat != '') {
            $obat = Obat::find($request->obat);
            $obat->stok = $obat->stok + $rekam->jumlahobat - $request->jumlahobat;
            $obat->save();
        }

        // // $rekam->layanan = $validated['layanan'];
        $rekam->keluhan = $validated['keluhan'];
        $rekam->id_dokter = $validated['dokter'];
        $rekam->diagnosa = $validated['diagnosa'];

        $rekam->id_obat = $request->obat;
        $rekam->jumlahobat = $request->jumlahobat;
        $rekam->keterangan = $request->keterangan;
        $rekam->rawat = $request->Ruang;
        $rekam->darah = $request->Darah;
        $rekam->tinggi = $request->Tinggi;
        $rekam->berat = $request->Berat;
        $rekam->pinggang = $request->LingkarBadan;
        $rekam->save();

        $pasien = Pasien::findOrfail($validated['idpasien']);
        $rekam = Rekam::where('id_pasien', $validated['idpasien'])->whereNotNull('diagnosa')->get();

        return back()->with('success', 'Data terupdate');
    }

    public function indexlaporan()
    {
        return view('laporan-harian', [
            'data' => Rekam::where('laporan', 1)->whereNotNull('diagnosa')->get(),
            'count' => 0
        ]);
    }

    public function clearlaporan()
    {
        Rekam::where('laporan', 1)->update(['laporan' => 2]);
        return redirect('/laporan-harian')->with('success', 'Berhasil clear data');
    }
}
