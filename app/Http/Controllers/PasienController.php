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

        $sid    = "AC01dae14034bd9fcbf4d4bc2a2ea30887";
        $token  = "64d28bb60c6949fcceada85553157360";
        $twilio_whatsapp_number = "+14155238886";
        $twilio_client = new Client($sid, $token);
        $this->validate(
            $request,
            [
                'Nama' => 'required',
                'Alamat' => 'required',
                'Lahir' => 'required',
                'NIK' => 'required',
                'Kelamin' => 'required',
                'Telepon' => 'required',
                'Agama' => 'required',
                // 'Pendidikan' => 'required',
                'Pekerjaan' => 'required',
                // 'layanan' => 'required',
                'RekamMedis' => 'required',
                // 'doktor' => 'required',
                // 'g-recaptcha-response' => 'required|captcha'
                ]
                // [
                    //     'g-recaptcha-response' => [
                        //         'required' => 'Please verify that you are not a robot.',
                        //         'captcha' => 'Captcha error! try again later or contact site admin.',
                        //     ],
                        // ],
                    );
                    
                    $data = Pasien::where('nama', $request->Nama)->where('lahir', $request->Lahir)->get();
                    $client_number = $request->Telepon;
                    
                    $nomorAntrian = 1;
                    $cekData = Rekam::whereDate('created_at', Carbon::today())->latest()->first();
                    
                    if ($cekData) {
                        $nomorAntrian = $cekData->nomorantrian + 1;
                    }
                    $qrsize = 250;
                    $sekarang = Carbon::now();
                    $tanggal_hari_ini = Carbon::today()->format('dmy');
                    
                    if (count($data) > 0) {
                        foreach ($data as $row) :
                $Rekam = Rekam::create([
                    'nomorantrian' => "00" . $nomorAntrian,
                    'id_pasien' => $row->id,
                    // // 'layanan' => $request->layanan,
                    'keluhan' => $request->RekamMedis,
                    // 'id_dokter' => $request->doktor
                ]);

                $jam_daftar = $Rekam->created_at->format('H:i:s');
                $tanggal_daftar = $Rekam->created_at->format('d-m-Y');
                $qrcode = QrCode::size($qrsize)->generate("Nomor Antrian : $nomorAntrian\n" . "Nama : $request->Nama\n" . "Tanggal Daftar : $tanggal_daftar\n" . "Jam Daftar : $jam_daftar \n" . "Unique Code : $nomorAntrian$tanggal_hari_ini\n");

                return back()->with([
                    'success' => 'Data berhasil ditambahkan',
                    'nomorAntrian' => "00" . $nomorAntrian,
                    'nama' => $request->Nama,
                    'timestamps' => $Rekam->created_at->format('H:i:s'),
                    'tanggaldaftar' => $Rekam->created_at->format('d-m-Y'),
                    'qrcode' => $qrcode
                ]);
            endforeach;
        } else {
            $Pasien = Pasien::create([
                'nama' => ucwords(strtolower($request->Nama)),
                'alamat' => $request->Alamat,
                'lahir' => $request->Lahir,
                'nik' => $request->NIK,
                'kelamin' => $request->Kelamin,
                'telepon' => $request->Telepon,
                'agama' => $request->Agama,
                // // 'pendidikan' => $request->Pendidikan,
                'pekerjaan' => $request->Pekerjaan
            ]);

            // $kode= 100000+ (integer)$Pasien -> id ;
            // $nomer= substr($kode, 1, 5). $Pasien -> lahir -> format ('dmy');
            // $Pasien -> kodepasien = $nomer ;
            // $Pasien -> save();
            $nomer = $Pasien->lahir->format('dmy');
            $Pasien->kodepasien = $nomer;
            $Pasien->save();

            $latestpasien = Pasien::all()->last();


            $jam_daftar = $Pasien->created_at->format('H:i:s');
            $tanggal_daftar = $Pasien->created_at->format('d-m-Y');
            $unique_code = "$nomorAntrian$tanggal_hari_ini";
            $qrcode = QrCode::format('png')
                            ->size($qrsize)
                            ->generate("Nomor Antrian : $nomorAntrian\n" . "Nama : $request->Nama\n" . "Tanggal Daftar : $tanggal_daftar\n" . "Jam Daftar : $jam_daftar\n" . "Unique Code : $unique_code");

            $output_file = '/img/qr-code/img-' . $unique_code . '.png';
            Storage::disk('public')->put($output_file, $qrcode); //storage/app/public/img/qr-code/img-1557309130.png

            Rekam::create([
                'nomorantrian' => "00" . $nomorAntrian,
                'id_pasien' => $latestpasien->id,
                // // 'layanan' => $request->layanan,
                'keluhan' => $request->RekamMedis,
                // 'id_dokter' => $request->doktor
            ]);
            
            $message = $twilio_client->messages
                ->create(
                    "whatsapp:".$client_number,
                    [
                        "from" => "whatsapp:" . $twilio_whatsapp_number,
                        "body" => "Terima kasih telah mendaftar di Klinik Desita.\nNomor antrian anda adalah *$nomorAntrian*\n Nama : $request->Nama\n Tanggal Daftar : $tanggal_daftar\n Jam Daftar : $jam_daftar\n". "Link *QR Code* : https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=$unique_code\n"
                        // "mediaUrl" => [url("http://127.0.0.1:8000/storage".$output_file)]
                    ]
                );

            // Log hasil pengiriman
            Log::info("Message sent: " . $message->sid);

            return back()->with([
                'success' => 'Data berhasil ditambahkan',
                'nomorAntrian' => "00" . $nomorAntrian,
                'nama' => $request->Nama,
                'timestamps' => $Pasien->created_at->format('H:i:s'),
                'tanggaldaftar' => $Pasien->created_at->format('d-m-Y'),
                'qrcode' => $qrcode,
                'qrpath' => asset("storage".$output_file),
                "message" => "Message sent: " . $message
            ]);
        }


        // return request()->all();
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
