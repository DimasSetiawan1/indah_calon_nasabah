<?php

namespace Database\Seeders;

use App\Models\Criteria;
use App\Models\Customer;
use App\Models\Assessment;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 0. Users
        \App\Models\User::factory()->create([
            'name' => 'Admin FIF',
            'username' => 'admin',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);

        \App\Models\User::factory()->create([
            'name' => 'MCE FIF',
            'username' => 'mce',
            'password' => bcrypt('password'),
            'role' => 'mce'
        ]);

        \App\Models\User::factory()->create([
            'name' => 'CAP FIF',
            'username' => 'cap',
            'password' => bcrypt('password'),
            'role' => 'cap'
        ]);

        // 1. Data Kriteria
        $criterias = [
            ['code' => 'C1', 'name' => 'Penghasilan Per Bulan (Juta Rp)', 'type' => 'benefit'],
            ['code' => 'C2', 'name' => 'Status Tempat Tinggal (1=Sewa, 2=Keluarga, 3=Milik Sendiri)', 'type' => 'benefit'],
            ['code' => 'C3', 'name' => 'Jumlah Tanggungan (Orang)', 'type' => 'cost'],
            ['code' => 'C4', 'name' => 'Riwayat Kredit / SLIK (1=Macet, 2=Kurang, 3=Lancar)', 'type' => 'benefit'],
            ['code' => 'C5', 'name' => 'Uang Muka / DP (Juta Rp)', 'type' => 'benefit'],
        ];

        $criteriaModels = [];
        foreach ($criterias as $c) {
            $criteriaModels[$c['code']] = Criteria::create($c);
        }

        // 2. Data Calon Nasabah
        $customers = [
            ['nik' => '3201010000000001', 'name' => 'Budi Santoso', 'address' => 'Jl. Merdeka No. 10, Jakarta', 'phone' => '081234567890'],
            ['nik' => '3201010000000002', 'name' => 'Siti Aminah', 'address' => 'Jl. Sudirman No. 45, Bandung', 'phone' => '082345678901'],
            ['nik' => '3201010000000003', 'name' => 'Andi Wijaya', 'address' => 'Jl. Diponegoro No. 8, Surabaya', 'phone' => '083456789012'],
            ['nik' => '3201010000000004', 'name' => 'Dewi Lestari', 'address' => 'Jl. Pahlawan No. 22, Semarang', 'phone' => '084567890123'],
            ['nik' => '3201010000000005', 'name' => 'Rahmat Hidayat', 'address' => 'Jl. Gajah Mada No. 1, Yogyakarta', 'phone' => '085678901234'],
        ];

        $customerModels = [];
        foreach ($customers as $c) {
            $customerModels[] = Customer::create($c);
        }

        // 3. Data Penilaian (Assessments)
        // Penilaian disesuaikan dengan range kriteria di atas agar mendekati real
        $assessmentsData = [
            // Budi Santoso: Penghasilan 5jt, Milik Sendiri, 2 Tanggungan, Lancar, DP 3jt
            0 => ['C1' => 5.0, 'C2' => 3, 'C3' => 2, 'C4' => 3, 'C5' => 3.0], 
            
            // Siti Aminah: Penghasilan 3.5jt, Keluarga, 1 Tanggungan, Lancar, DP 1.5jt
            1 => ['C1' => 3.5, 'C2' => 2, 'C3' => 1, 'C4' => 3, 'C5' => 1.5],
            
            // Andi Wijaya: Penghasilan 8jt, Sewa, 4 Tanggungan, Kurang, DP 4jt
            2 => ['C1' => 8.0, 'C2' => 1, 'C3' => 4, 'C4' => 2, 'C5' => 4.0],
            
            // Dewi Lestari: Penghasilan 12jt, Milik Sendiri, 0 Tanggungan, Lancar, DP 5jt (Nasabah Terbaik)
            3 => ['C1' => 12.0, 'C2' => 3, 'C3' => 0, 'C4' => 3, 'C5' => 5.0],
            
            // Rahmat Hidayat: Penghasilan 2.5jt, Sewa, 3 Tanggungan, Macet, DP 1jt (Nasabah Terburuk)
            4 => ['C1' => 2.5, 'C2' => 1, 'C3' => 3, 'C4' => 1, 'C5' => 1.0],
        ];

        foreach ($customerModels as $index => $customer) {
            $scores = $assessmentsData[$index];
            foreach ($scores as $code => $val) {
                Assessment::create([
                    'nasabah_id'  => $customer->id,
                    'kriteria_id' => $criteriaModels[$code]->id,
                    'value'       => $val,
                ]);
            }
        }
    }
}
