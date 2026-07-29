<?php

namespace Database\Seeders;

use App\Models\Criteria;
use App\Models\Subcriteria;
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
            ['code' => 'C1', 'name' => 'Penghasilan', 'type' => 'benefit'],
            ['code' => 'C2', 'name' => 'Pekerjaan', 'type' => 'benefit'],
            ['code' => 'C3', 'name' => 'Riwayat Kredit', 'type' => 'benefit'],
            ['code' => 'C4', 'name' => 'Jaminan Kendaraan', 'type' => 'benefit'],
            ['code' => 'C5', 'name' => 'Lama Tempat Tinggal', 'type' => 'benefit'],
        ];

        $criteriaModels = [];
        foreach ($criterias as $c) {
            $criteriaModels[$c['code']] = Criteria::create($c);
        }

        // 2. Data Subkriteria
        $subcriteriasData = [
            'C1' => [
                ['name' => '> Rp 7.000.000 (Sangat Tinggi)', 'score' => 5],
                ['name' => 'Rp 6.000.000 - Rp 6.999.999 (Tinggi)', 'score' => 4],
                ['name' => 'Rp 5.000.000 - Rp 5.999.999 (Sedang / Setara UMR)', 'score' => 3],
                ['name' => 'Rp 4.000.000 - Rp 4.499.999 (Rendah)', 'score' => 2],
                ['name' => '< Rp 2.500.000 (Sangat Rendah)', 'score' => 1],
            ],
            'C2' => [
                ['name' => 'PNS / TNI / POLRI / Pegawai BUMN', 'score' => 5],
                ['name' => 'Pegawai Swasta Tetap / Profesional', 'score' => 4],
                ['name' => 'Wiraswasta (Usaha > 2 tahun)', 'score' => 3],
                ['name' => 'Pegawai Kontrak / Honorer', 'score' => 2],
                ['name' => 'Buruh Harian / Pekerja Lepas / Pensiunan', 'score' => 1],
            ],
            'C3' => [
                ['name' => 'Sangat Baik (Kol 1 - Lancar, tidak pernah menunggak)', 'score' => 5],
                ['name' => 'Baik (Belum pernah ada riwayat kredit / Pemula)', 'score' => 4],
                ['name' => 'Cukup (Kol 2 - Dalam Perhatian Khusus)', 'score' => 3],
                ['name' => 'Buruk (Kol 3 & 4 - Kurang Lancar & Diragukan)', 'score' => 2],
                ['name' => 'Sangat Buruk (Kol 5 - Macet / Blacklist)', 'score' => 1],
            ],
            'C4' => [
                ['name' => 'Sangat Baru (0 - 1 tahun)', 'score' => 5],
                ['name' => 'Baru (2 - 3 tahun)', 'score' => 4],
                ['name' => 'Menengah (4 - 5 tahun)', 'score' => 3],
                ['name' => 'Tua (6 - 8 tahun)', 'score' => 2],
                ['name' => 'Sangat Tua (> 8 tahun)', 'score' => 1],
            ],
            'C5' => [
                ['name' => 'Rumah Sendiri (Lunas / Bersertifikat)', 'score' => 5],
                ['name' => 'Rumah Sendiri (Masih proses KPR/Cicilan)', 'score' => 4],
                ['name' => 'Rumah Keluarga / Orang Tua', 'score' => 3],
                ['name' => 'Sewa / Kontrak Tahunan (> 1 tahun)', 'score' => 2],
                ['name' => 'Kos / Kontrak Bulanan (< 1 tahun)', 'score' => 1],
            ],
        ];

        foreach ($subcriteriasData as $code => $subs) {
            $crit = $criteriaModels[$code];
            foreach ($subs as $sub) {
                Subcriteria::create([
                    'kriteria_id' => $crit->id,
                    'name' => $sub['name'],
                    'score' => $sub['score'],
                ]);
            }
        }

        // 3. Data Calon Nasabah Dummy (dengan Penilaian Awal)
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

        $assessmentsData = [
            0 => ['C1' => 4, 'C2' => 4, 'C3' => 5, 'C4' => 4, 'C5' => 5], // Budi Santoso
            1 => ['C1' => 3, 'C2' => 3, 'C3' => 4, 'C4' => 3, 'C5' => 3], // Siti Aminah
            2 => ['C1' => 5, 'C2' => 3, 'C3' => 3, 'C4' => 2, 'C5' => 2], // Andi Wijaya
            3 => ['C1' => 5, 'C2' => 5, 'C3' => 5, 'C4' => 5, 'C5' => 5], // Dewi Lestari
            4 => ['C1' => 1, 'C2' => 1, 'C3' => 1, 'C4' => 1, 'C5' => 1], // Rahmat Hidayat
        ];

        foreach ($customerModels as $index => $customer) {
            $scores = $assessmentsData[$index];
            foreach ($scores as $code => $val) {
                Assessment::create([
                    'nasabah_id' => $customer->id,
                    'kriteria_id' => $criteriaModels[$code]->id,
                    'value' => $val,
                ]);
            }
        }
    }
}
