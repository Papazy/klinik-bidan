<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Jadwal;
use App\Models\Poli;
use Illuminate\Support\Facades\Hash;

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
    }
}
