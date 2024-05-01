<?php

namespace App\Http\Controllers;

use App\Models\Antrian;
use App\Models\Rekam;
use App\Models\Pasien;
use App\Models\Dokter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
// require_once 'vendor/autoload.php';
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RekamController extends Controller
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

        // Inisialisasi klien Twilio dan data Twilio
        $sid = "AC01dae14034bd9fcbf4d4bc2a2ea30887";
        $token = "64d28bb60c6949fcceada85553157360";
        $twilio_whatsapp_number = "+14155238886";
        $twilio_client = new Client($sid, $token);


        $validate = $request->validate([
            'id_player' => 'required',
            'keluhan' => 'required',
            'jadwal' => 'required',
        ],
        );

        $data_user = Pasien::where('id', $validate['id_player'])->first();
        // Antrian
        $obj_antrian = new Antrian();
        $nomorAntrian = $obj_antrian->generateNoAntrian()['no_antrian'];
        $tanggal = $obj_antrian->generateNoAntrian()['tanggal'];

        
        $data_antrian = Antrian::create([
            'no_antrian' => $nomorAntrian,
            'pasien_id' => $validate['id_player'],
            'jadwal_praktek' => $validate['jadwal'],
            'jadwal_antrian' => $tanggal,
            'tanggal_daftar_antrian' => Carbon::now(),
        ]);

        $Rekam = Rekam::create([
            'id_antrian' => $data_antrian->id,
            'id_pasien' => $validate['id_player'],
            'keluhan' => $validate['keluhan'],
        ]);

        $unique_code = "$nomorAntrian" . Carbon::today()->format('dmy');
        $qrcode = QrCode::format('png')
            ->size(300)
            ->generate("Nomor Antrian: $nomorAntrian\nNama: $request->Nama\nTanggal Daftar: " . Carbon::today()->format('d-m-Y') . "\nJam Daftar: " . Carbon::now()->format('H:i:s') . "\nUnique Code: $unique_code");

        $output_file = '/img/qr-code/img-' . $unique_code . '.png';
        Storage::disk('public')->put($output_file, $qrcode); //storage/app/public/img/qr-code/img-1557309130.png


        $latestrekam = Rekam::all()->last();
        $pasienid = $latestrekam->id_pasien;
        $pasientable = Pasien::where('id', $pasienid)->get();
         // Kirim pesan WhatsApp
         $message = $twilio_client->messages->create(
            "whatsapp:$data_user->telepon",
            [
                "from" => "whatsapp:$twilio_whatsapp_number",
                "body" => "Terima kasih telah mendaftar di Klinik Desita.\nNomor antrian Anda adalah *$nomorAntrian*\nNama: $request->Nama\nTanggal Daftar: " . Carbon::today()->format('d-m-Y') . "\nJam Daftar: " . Carbon::now()->format('H:i:s') . "\nLink *QR Code*: https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=$unique_code\n"
            ]
        );
        foreach ($pasientable as $row):

            return redirect('pasien-lama')->with([
                'success' => 'Data berhasil ditambahkan',
                'nomorAntrian' => "$nomorAntrian",
                'kodepasien' => $data_user->kodepasien,
                'nama' => $data_user->nama,
                'timestamps' => Carbon::now()->format('H:i:s'),
                'tanggaldaftar' => Carbon::today()->format('d-m-Y'),
                'jadwalAntrian' => $data_antrian->jadwal_antrian->format('d-m-Y'),
                'jadwalPraktik' => $data_antrian->jadwal_praktek,
                'qrcode' => $qrcode,
                'qrpath' => asset("storage" . $output_file), // Tidak perlu asset karena QR Code dihasilkan secara dinamis
                "message" => "Message sent: " . $message->sid
            ]);

        endforeach;
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
        $rekam = Rekam::find($id);
        return view('antrian-pasien-edit-form',[
            'rekam' => $rekam,
            'dokter' => Dokter::all()
        ]);
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
        $validated = $request->validate([
            'layanan' => 'required',
            'keluhan' => 'required',
            'dokter' => 'required'
        ]);

        $rekam = Rekam::find($id);
        $rekam->layanan = $validated['layanan'];
        $rekam->keluhan = $validated['keluhan'];
        $rekam->id_dokter = $validated['dokter'];
        $rekam->save();

        return redirect('antrian-pasien-admin')->with('success', 'Data TerUpdate');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $rekam = Rekam::find($id);
        $rekam->delete();
        return back()->with('success', 'Data Terhapus');
    }

    public function edits($id)
    {
        $rekam = Rekam::find($id);
        return view('rekam-pasien-edit-form',[
            'rekam' => $rekam
        ]);
    }
}
