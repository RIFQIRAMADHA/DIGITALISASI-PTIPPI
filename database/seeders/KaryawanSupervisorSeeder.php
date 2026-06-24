<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Produksi\Master\Karyawan;
use Illuminate\Support\Facades\Hash;

class KaryawanSupervisorSeeder extends Seeder
{
    public function run(): void
    {
        Karyawan::updateOrCreate(
            ['NRPKaryawan' => '222'], // NRP unik untuk login
            [
                'IdKaryawan' => 'EMP001',
                'NamaKaryawan' => 'Supervisor',
                'PasswordKaryawan' => bcrypt('supervisor123'), // Sesuai bcrypt di Controller
                'Jabatan' => 'supervisor', // Harus masuk dalam listJabatan Bapak
                'Status' => 1,
                'create_by' => 'System Docker',
            ]
        );
    }
}