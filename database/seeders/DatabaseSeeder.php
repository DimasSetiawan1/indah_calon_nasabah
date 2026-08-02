<?php

namespace Database\Seeders;

use App\Models\Criteria;
use App\Models\Subcriteria;
use App\Models\Customer;
use App\Models\Assessment;
use App\Http\Controllers\TopsisController;
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

        // 1. Data Kriteria dengan Bobot Default Hasil BWM
        $criterias = [
            ['code' => 'C1', 'name' => 'Penghasilan', 'type' => 'benefit', 'weight' => 0.2428],
            ['code' => 'C2', 'name' => 'Pekerjaan', 'type' => 'benefit', 'weight' => 0.1730],
            ['code' => 'C3', 'name' => 'Riwayat Kredit', 'type' => 'benefit', 'weight' => 0.3856],
            ['code' => 'C4', 'name' => 'Jaminan Kendaraan', 'type' => 'benefit', 'weight' => 0.1214],
            ['code' => 'C5', 'name' => 'Tempat Tinggal', 'type' => 'benefit', 'weight' => 0.0771],
        ];

        $criteriaModels = [];
        foreach ($criterias as $c) {
            $criteriaModels[$c['code']] = Criteria::create($c);
        }

        // 2. Data Subkriteria (Sesuai Dokumen Paper 100%)
        $subcriteriasData = [
            'C1' => [
                ['name' => '> Rp 10.000.000 (Sangat Tinggi)', 'score' => 5],
                ['name' => 'Rp 6.000.000 - Rp 10.000.000 (Tinggi)', 'score' => 4],
                ['name' => 'Rp 3.500.000 - Rp 5.999.999 (Sedang / Setara UMR)', 'score' => 3],
                ['name' => 'Rp 1.500.000 - Rp 3.499.999 (Rendah)', 'score' => 2],
                ['name' => '< Rp 1.500.000 (Sangat Rendah)', 'score' => 1],
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

        // 3. Data 20 Calon Nasabah Real (diambil dari DATA NASABAH.csv)
        $customers = [
            ['nik' => '3175063101020000', 'name' => 'ADITYA YUDA PRATAMA', 'address' => 'UJUNG MENTENG NO 20 RT.003 RW.002 KEL.UJUNG MENTENG KEC.CAKUNG', 'phone' => '085775027271', 'scores' => ['C1' => 4, 'C2' => 4, 'C3' => 5, 'C4' => 4, 'C5' => 3]],
            ['nik' => '3275024204820020', 'name' => 'KIKIE LESTARI', 'address' => 'JL.MELUR 5 NO 31 RT.003 RW.010 KEL.JAKA SAMPURNA KEC.BEKASI BARAT', 'phone' => '087724973686', 'scores' => ['C1' => 4, 'C2' => 4, 'C3' => 4, 'C4' => 1, 'C5' => 2]],
            ['nik' => '3275110604720000', 'name' => 'MASIM', 'address' => 'KP.BABAKAN RT.003 RW.004 KEL.MUSTIKASARI KEC.MUSTIKA JAYA', 'phone' => '081383509284', 'scores' => ['C1' => 3, 'C2' => 3, 'C3' => 4, 'C4' => 2, 'C5' => 5]],
            ['nik' => '3275114208710000', 'name' => 'NENI LENIAWATI', 'address' => 'JL.DURIAN 9 BLK D 10 NO 2 RT.005 RW.016 KEL.PADURENAN KEC.MUSTIKA JAYA', 'phone' => '082114013749', 'scores' => ['C1' => 3, 'C2' => 1, 'C3' => 3, 'C4' => 1, 'C5' => 5]],
            ['nik' => '3275020403670010', 'name' => 'DAHLAN', 'address' => 'JL BINTARA 1 RT.008 RW.002 KEL.BINTARA KEC.BEKASI BARAT', 'phone' => '081381137353', 'scores' => ['C1' => 3, 'C2' => 3, 'C3' => 4, 'C4' => 4, 'C5' => 5]],
            ['nik' => '3216184404920000', 'name' => 'FITRIYANI', 'address' => 'KP RAWA ATUG RT.002 RW.006 KEL.CIBENING KEC.SETU', 'phone' => '085280323377', 'scores' => ['C1' => 3, 'C2' => 3, 'C3' => 3, 'C4' => 2, 'C5' => 5]],
            ['nik' => '3175076003780000', 'name' => 'AMALIA CATRIEN PERTIWI', 'address' => 'JL.MAWAR MERAH RAYA BLOK 32 NO 5 RT.009 RW.012 KEL.MALAKA JAYA KEC.DUREN SAWIT', 'phone' => '085697150679', 'scores' => ['C1' => 5, 'C2' => 4, 'C3' => 5, 'C4' => 3, 'C5' => 5]],
            ['nik' => '3275011610850010', 'name' => 'ADE SUPRIYADI', 'address' => 'TELUK PUCUNG RT.001 RW.001 KEL.TELUK PUCUNG KEC.BEKASI UTARA', 'phone' => '0895333142207', 'scores' => ['C1' => 3, 'C2' => 4, 'C3' => 4, 'C4' => 3, 'C5' => 5]],
            ['nik' => '3174056005010000', 'name' => 'FANY INDRIANY', 'address' => 'JL PATRIOT VI NO 128 RT.002 RW.024 KEL.SETIAMEKAR KEC.TAMBUN UTARA', 'phone' => '081943651353', 'scores' => ['C1' => 3, 'C2' => 4, 'C3' => 4, 'C4' => 2, 'C5' => 3]],
            ['nik' => '3275056212760030', 'name' => 'FINA DWIHANA', 'address' => 'JL PANDEGA III NO 145 RT.003 RW.007 KEL.BOJONG RAWALUMBU KEC.RAWALUMBU', 'phone' => '085900018878', 'scores' => ['C1' => 3, 'C2' => 2, 'C3' => 3, 'C4' => 1, 'C5' => 5]],
            ['nik' => '3216022303000000', 'name' => 'YUDHA PUTRA PERWIRA', 'address' => 'PUP SEKTOR V BLOK F1 NO 7 RT.001 RW.023 KEL.BAHAGIA KEC.BABELAN', 'phone' => '089692220700', 'scores' => ['C1' => 3, 'C2' => 4, 'C3' => 4, 'C4' => 5, 'C5' => 3]],
            ['nik' => '3275046305900000', 'name' => 'HENI ATIKAWATI', 'address' => 'KP PULO CEGER RT.004 RW.003 KEL.JAKASETIA KEC.BEKASI SELATAN', 'phone' => '082210982854', 'scores' => ['C1' => 2, 'C2' => 1, 'C3' => 2, 'C4' => 1, 'C5' => 2]],
            ['nik' => '3275034809920030', 'name' => 'KEZIA ELIZABETH NABABAN', 'address' => 'DUTA HARAPAN BLOK AF V NO 2 RT.003 RW.010 KEL.HARAPAN BARU KEC.BEKASI UTARA', 'phone' => '081514663040', 'scores' => ['C1' => 4, 'C2' => 4, 'C3' => 5, 'C4' => 2, 'C5' => 1]],
            ['nik' => '3175025001000000', 'name' => 'NING SALSABILA P WIJAYA', 'address' => 'KP.MARIUK RT.003 RW.004 KEL.GANDASARI KEC.CIKARANG BARAT', 'phone' => '087811865677', 'scores' => ['C1' => 3, 'C2' => 3, 'C3' => 4, 'C4' => 4, 'C5' => 3]],
            ['nik' => '3216060808940000', 'name' => 'RADITYA SURIPTO PUTRA', 'address' => 'BEKASI TIMUR PERMAI BLOK C3 NO 13 RT.020 RW.012 KEL.SETIAMEKAR KEC.TAMBUN SELATAN', 'phone' => '085121353299', 'scores' => ['C1' => 4, 'C2' => 4, 'C3' => 5, 'C4' => 5, 'C5' => 5]],
            ['nik' => '3175035908950000', 'name' => 'SHEILA ARISTA SUWANDI', 'address' => 'BEKASI TIMUR REGENCY V CLUSTER FARGESIA BLOK F9 NO 72 RT.001 RW.012 KEL.SUMUR BATU KEC.BANTARGEBANG', 'phone' => '089515390087', 'scores' => ['C1' => 4, 'C2' => 4, 'C3' => 4, 'C4' => 1, 'C5' => 5]],
            ['nik' => '3275044712810020', 'name' => 'HERLINA SETYOSARI', 'address' => 'KOMP DDN BLOK A NO 3 RT.001 RW.010 KEL.JATIWARINGIN KEC.PONDOK GEDE', 'phone' => '087734252015', 'scores' => ['C1' => 3, 'C2' => 4, 'C3' => 4, 'C4' => 1, 'C5' => 5]],
            ['nik' => '3275046609010010', 'name' => 'KARINA PUTRI WIJAYA', 'address' => 'KP KAYURINGIN RT.006 RW.012 KEL.KAYURINGIN JAYA KEC.BEKASI SELATAN', 'phone' => '081286291374', 'scores' => ['C1' => 3, 'C2' => 4, 'C3' => 4, 'C4' => 4, 'C5' => 3]],
            ['nik' => '3275026401920000', 'name' => 'VIDIA CAHYA ANGGRAINI', 'address' => 'BEKASI TIMUR REGENCY V CLUSTER FARGESIA BLOK F9 NO 56 RT.001 RW.012 KEL.SUMUR BATU KEC.BANTARGEBANG', 'phone' => '081311364533', 'scores' => ['C1' => 3, 'C2' => 4, 'C3' => 4, 'C4' => 3, 'C5' => 5]],
            ['nik' => '3275080508800020', 'name' => 'YOSE ANANDA', 'address' => 'VILLA NUSA INDAH 2 BLOK U 21 NO 7 RT.002 RW.020 KEL.BOJONG KULUR KEC.GUNUNG PUTRI', 'phone' => '081294129993', 'scores' => ['C1' => 4, 'C2' => 5, 'C3' => 5, 'C4' => 4, 'C5' => 5]],
        ];

        foreach ($customers as $c) {
            $scores = $c['scores'];
            unset($c['scores']);

            $customerModel = Customer::create($c);

            foreach ($scores as $code => $val) {
                Assessment::create([
                    'nasabah_id' => $customerModel->id,
                    'kriteria_id' => $criteriaModels[$code]->id,
                    'value' => $val,
                ]);
            }
        }

        // Kalkulasi otomatis awal perangkingan TOPSIS
        app(TopsisController::class)->recalculateAll();
    }
}
