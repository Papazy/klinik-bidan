<?php

namespace App\Http\Controllers;

use App\Models\Antrian;
use App\Models\Rekam;
use App\Models\Pasien;
use App\Models\Dokter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
        $validate = $request->validate([
            'id_player' => 'required',
            // 'layanan' => 'required',
            'keluhan' => 'required',
            'jadwal' => 'required',
        	// 'g-recaptcha-response' => 'required|captcha'
        ],
        // [
        //     'g-recaptcha-response' => [
        //         'required' => 'Please verify that you are not a robot.',
        //         'captcha' => 'Captcha error! try again later or contact site admin.',
        //     ],
        // ],
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
            'nomorantrian' => "00" . $nomorAntrian,
            'id_pasien' => $validate['id_player'],
            // 'layanan' => $validate['layanan'],
            'keluhan' => $validate['keluhan'],
            // 'jadwal' => $validate['jadwal'],
            // 'id_dokter' => $validate['dokter']
        ]);

        $latestrekam = Rekam::all()->last();
        $pasienid = $latestrekam->id_pasien;
        $pasientable = Pasien::where('id', $pasienid)->get();

        foreach ($pasientable as $row):

            return redirect('pasien-lama')->with([
                'addsuccess' => 'Data berhasil ditambahkan',
                'nomorAntrian' => "" . $nomorAntrian,
                'nama' => $row->nama,
                'timestamps' => $Rekam->created_at->format('H:i:s'),
                'tanggaldaftar' => $Rekam->created_at->format('d-m-Y')
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
