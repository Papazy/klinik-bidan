<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Jadwal;
use App\Models\Poli;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'admin',
            'role' => 'admin',
            'email' => 'admin@gmail.com',
            'is_admin' => 1,
            'is_superadmin' => 1,
            'password' => Hash::make('admin'),
        ]);
        // $polis = [
        //     ['name' => 'Poli Umum'],
        //     ['name' => 'Poli Bedah'],
        //     ['name' => 'Poli Anak'],
        //     ['name' => 'Poli Kandungan dan Obstetri'],
        //     ['name' => 'Poli Gigi'],
        //     ['name' => 'Poli Mata'],
        //     ['name' => 'Poli Jantung'],
        //     ['name' => 'Poli Kulit dan Kelamin'],
        //     ['name' => 'Poli Psikiatri'],
        //     ['name' => 'Poli THT (Telinga, Hidung, Tenggorokan)'],
        //     ['name' => 'Poli Gizi'],
        //     ['name' => 'Poli Diabetes'],
        //     ['name' => 'Poli Rehabilitasi'],
        //     ['name' => 'Poli Kesehatan Jiwa'],
        //     ['name' => 'Poli Kesehatan Wanita'],
        // ];

        // // Masukkan data ke dalam tabel 'polis'
        // Poli::insert($polis);

       Jadwal::create([
            'jadwalpraktek' => '08:00-16:00',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Jadwal 18:00-02:00
       Jadwal::create([
            'jadwalpraktek' => '18:00-02:00',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Obat seeder
        // Seed data for jenis table
        DB::table('jenis')->insert([
            ['jenisobat' => 'Tablet'],
            ['jenisobat' => 'Kapsul'],
            ['jenisobat' => 'Sirup'],
            // Add more jenisobat data as needed
        ]);

        // Seed data for obats table
        DB::table('obats')->insert([
            [
                'kodeobat' => 'OB001',
                'stok' => 100,
                'id_jenis' => 1, // Assuming Tablet has ID 1
                'nama' => 'Paracetamol',
                'dosis' => '500 mg',
                'harga' => '5000',
                'expired' => '2024-12-31',
                'photo' => 'paracetamol.jpg',
            ],
            [
                'kodeobat' => 'OB002',
                'stok' => 50,
                'id_jenis' => 2, // Assuming Kapsul has ID 2
                'nama' => 'Amoxicillin',
                'dosis' => '250 mg',
                'harga' => '8000',
                'expired' => '2023-10-15',
                'photo' => 'amoxicillin.jpg',
            ],
            // Add more obat data as needed
        ]);

        DB::table('pasiens')->insert([
            [
                'kodepasien' => 'KP001',
                'nama' => 'John Doe',
                'alamat' => 'Jl. Contoh No. 123',
                'lahir' => '1990-05-15',
                'nik' => '1234567890',
                'kelamin' => 'Laki-laki',
                'telepon' => '081234567890',
                'agama' => 'Islam',
                'pekerjaan' => 'PNS',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kodepasien' => 'KP002',
                'nama' => 'Jane Doe',
                'alamat' => 'Jl. Contoh No. 456',
                'lahir' => '1995-08-20',
                'nik' => '0987654321',
                'kelamin' => 'Perempuan',
                'telepon' => '085678901234',
                'agama' => 'Kristen',
                'pekerjaan' => 'Guru',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Add more pasien data as needed
        ]);

        // Seed data for rekams table
        DB::table('rekams')->insert([
            [
                'laporan' => 1,
                'id_pasien' => 1,
                'nomorantrian' => '001',
                'tanggalperiksa' => '2024-05-01',
                'keluhan' => 'Demam',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'laporan' => 1,
                'id_pasien' => 2,
                'nomorantrian' => '002',
                'tanggalperiksa' => '2024-05-02',
                'keluhan' => 'Sakit kepala',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Add more rekam data as needed
        ]);
    }
}
