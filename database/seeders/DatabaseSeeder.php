<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Pasien;
use App\Models\Antrian;
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

        Jadwal::create([
            'jadwalpraktek' => '08:00-12:00',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Jadwal::create([
            'jadwalpraktek' => '14:00-18:00',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Jadwal::create([
            'jadwalpraktek' => '20:00-22:00',
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
                'telepon' => '+6282267450565',
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
                'telepon' => '+6282267450565',
                'agama' => 'Kristen',
                'pekerjaan' => 'Guru',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kodepasien' => 'KP003',
                'nama' => 'Michael Jackson',
                'alamat' => 'Neverland Ranch',
                'lahir' => '1958-08-29',
                'nik' => '3456789012',
                'kelamin' => 'Laki-laki',
                'telepon' => '+6282267450565',
                'agama' => 'Katolik',
                'pekerjaan' => 'Penyanyi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kodepasien' => 'KP004',
                'nama' => 'Albert Einstein',
                'alamat' => '123 Relativity Street',
                'lahir' => '1879-03-14',
                'nik' => '4567890123',
                'kelamin' => 'Laki-laki',
                'telepon' => '+6282267450565',
                'agama' => 'Ateis',
                'pekerjaan' => 'Fisikawan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kodepasien' => 'KP005',
                'nama' => 'Marie Curie',
                'alamat' => 'Paris, Perancis',
                'lahir' => '1867-11-07',
                'nik' => '5678901234',
                'kelamin' => 'Perempuan',
                'telepon' => '+6282267450565',
                'agama' => 'Katolik',
                'pekerjaan' => 'Peneliti',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kodepasien' => 'KP006',
                'nama' => 'Nikola Tesla',
                'alamat' => 'Smiljan, Kroasia',
                'lahir' => '1856-07-10',
                'nik' => '6789012345',
                'kelamin' => 'Laki-laki',
                'telepon' => '+6282267450565',
                'agama' => 'Ortodoks Serbia',
                'pekerjaan' => 'Fisikawan dan Insinyur Listrik',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kodepasien' => 'KP007',
                'nama' => 'Ada Lovelace',
                'alamat' => 'London, Inggris',
                'lahir' => '1815-12-10',
                'nik' => '7890123456',
                'kelamin' => 'Perempuan',
                'telepon' => '+6282267450565',
                'agama' => 'Anglikan',
                'pekerjaan' => 'Matematikawan dan Penulis',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kodepasien' => 'KP008',
                'nama' => 'Steve Jobs',
                'alamat' => 'Cupertino, California, Amerika Serikat',
                'lahir' => '1955-02-24',
                'nik' => '8901234567',
                'kelamin' => 'Laki-laki',
                'telepon' => '+6282267450565',
                'agama' => 'Budha',
                'pekerjaan' => 'Pengusaha dan Penemu',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kodepasien' => 'KP009',
                'nama' => 'Coco Chanel',
                'alamat' => 'Paris, Perancis',
                'lahir' => '1883-08-19',
                'nik' => '9012345678',
                'kelamin' => 'Perempuan',
                'telepon' => '+6282267450565',
                'agama' => 'Ateis',
                'pekerjaan' => 'Perancang Mode',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kodepasien' => 'KP010',
                'nama' => 'Leonardo da Vinci',
                'alamat' => 'Vinci, Italia',
                'lahir' => '1452-04-15',
                'nik' => '0123456789',
                'kelamin' => 'Laki-laki',
                'telepon' => '+6282267450565',
                'agama' => 'Katolik',
                'pekerjaan' => 'Pelukis, Penemu, dan Polimatik',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kodepasien' => 'KP011',
                'nama' => 'Rosa Parks',
                'alamat' => 'Tuskegee, Alabama, Amerika Serikat',
                'lahir' => '1913-02-04',
                'nik' => '1234567890',
                'kelamin' => 'Perempuan',
                'telepon' => '+6282267450565',
                'agama' => 'Protestan',
                'pekerjaan' => 'Aktivis Hak Sipil',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kodepasien' => 'KP012',
                'nama' => 'Walt Disney',
                'alamat' => 'Chicago, Illinois, Amerika Serikat',
                'lahir' => '1901-12-05',
                'nik' => '2345678901',
                'kelamin' => 'Laki-laki',
                'telepon' => '+6282267450565',
                'agama' => 'Kristen',
                'pekerjaan' => 'Produser Film dan Pengusaha Hiburan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);



        // Antrian
        $jadwal1 = '08:00-12:00';
        $jadwal2 = '14:00-18:00';
        $jadwal3 = '20:00-22:00';
        $antrianData = [
            [
                'no_antrian' => '1',
                'pasien_id' => Pasien::where('nama', 'John Doe')->first()->id,
                'jadwal_praktek' => $jadwal1,
                'jadwal_antrian' => now(),
                'tanggal_daftar_antrian' => now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'no_antrian' => '2',
                'pasien_id' => Pasien::where('nama', 'Jane Doe')->first()->id,
                'jadwal_praktek' => $jadwal1,
                'jadwal_antrian' => now(),
                'tanggal_daftar_antrian' => now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Tambahkan data antrian lainnya di sini
            [
                'no_antrian' => '3',
                'pasien_id' => Pasien::where('nama', 'Michael Jackson')->first()->id,
                'jadwal_praktek' => $jadwal2,
                'jadwal_antrian' => now(),
                'tanggal_daftar_antrian' => now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'no_antrian' => '4',
                'pasien_id' => Pasien::where('nama', 'Albert Einstein')->first()->id,
                'jadwal_praktek' => $jadwal2,
                'jadwal_antrian' => now(),
                'tanggal_daftar_antrian' => now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'no_antrian' => '5',
                'pasien_id' => Pasien::where('nama', 'Jane Doe')->first()->id,
                'jadwal_praktek' => $jadwal1,
                'jadwal_antrian' => now(),
                'tanggal_daftar_antrian' => now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Tambahkan data antrian lainnya di sini
            [
                'no_antrian' => '6',
                'pasien_id' => Pasien::where('nama', 'Michael Jackson')->first()->id,
                'jadwal_praktek' => $jadwal2,
                'jadwal_antrian' => now(),
                'tanggal_daftar_antrian' => now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'no_antrian' => '7',
                'pasien_id' => Pasien::where('nama', 'Albert Einstein')->first()->id,
                'jadwal_praktek' => $jadwal2,
                'jadwal_antrian' => now(),
                'tanggal_daftar_antrian' => now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'no_antrian' => '8',
                'pasien_id' => Pasien::where('nama', 'Jane Doe')->first()->id,
                'jadwal_praktek' => $jadwal1,
                'jadwal_antrian' => now(),
                'tanggal_daftar_antrian' => now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Tambahkan data antrian lainnya di sini
            [
                'no_antrian' => '9',
                'pasien_id' => Pasien::where('nama', 'Michael Jackson')->first()->id,
                'jadwal_praktek' => $jadwal2,
                'jadwal_antrian' => now(),
                'tanggal_daftar_antrian' => now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'no_antrian' => '10',
                'pasien_id' => Pasien::where('nama', 'Albert Einstein')->first()->id,
                'jadwal_praktek' => $jadwal2,
                'jadwal_antrian' => now(),
                'tanggal_daftar_antrian' => now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'no_antrian' => '11',
                'pasien_id' => Pasien::where('nama', 'Jane Doe')->first()->id,
                'jadwal_praktek' => $jadwal1,
                'jadwal_antrian' => now(),
                'tanggal_daftar_antrian' => now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Tambahkan data antrian lainnya di sini
            [
                'no_antrian' => '12',
                'pasien_id' => Pasien::where('nama', 'Michael Jackson')->first()->id,
                'jadwal_praktek' => $jadwal2,
                'jadwal_antrian' => now(),
                'tanggal_daftar_antrian' => now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'no_antrian' => '13',
                'pasien_id' => Pasien::where('nama', 'Albert Einstein')->first()->id,
                'jadwal_praktek' => $jadwal2,
                'jadwal_antrian' => now(),
                'tanggal_daftar_antrian' => now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'no_antrian' => '14',
                'pasien_id' => Pasien::where('nama', 'Albert Einstein')->first()->id,
                'jadwal_praktek' => $jadwal2,
                'jadwal_antrian' => now(),
                'tanggal_daftar_antrian' => now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'no_antrian' => '15',
                'pasien_id' => Pasien::where('nama', 'Albert Einstein')->first()->id,
                'jadwal_praktek' => $jadwal2,
                'jadwal_antrian' => now(),
                'tanggal_daftar_antrian' => now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        
        // Seed data for rekams table
        DB::table('rekams')->insert([
            [
                'laporan' => 1,
                'id_pasien' => 1,
                'id_antrian' => 1,
                'tanggalperiksa' => '2024-05-01',
                'keluhan' => 'Demam',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'laporan' => 1,
                'id_pasien' => 2,
                'id_antrian' => 2,
                'tanggalperiksa' => '2024-05-02',
                'keluhan' => 'Sakit kepala',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Add more rekam data as needed
        ]);

        // Masukkan data antrian ke dalam database
        Antrian::insert($antrianData);
    }
}
