# Sistem Rekomendasi Kelayakan Nasabah (BWM & TOPSIS)

Repositori ini memuat _source code_ untuk skripsi dengan judul:
**"INTEGRASI BEST WORST METHOD (BWM) DAN TOPSIS DALAM MEREKOMENDASIKAN KELAYAKAN CALON NASABAH PADA PT FEDERAL INTERNASIONAL FINANCE"**

Sistem ini dibangun menggunakan _framework_ web **Laravel 11** dan **Bootstrap CSS** untuk antarmuka yang bersih dan responsif.

---

## Alur Perhitungan Sistem (Logika Metode)

Sistem ini menggabungkan dua metode pengambilan keputusan multikriteria (MCDM), yaitu **BWM** untuk mencari bobot, dan **TOPSIS** untuk merangking alternatif. Berikut adalah bedah alur logika perhitungan di dalam kode programnya:

### Fase 1: Perhitungan Bobot Kriteria dengan BWM

Tujuan dari metode ini adalah untuk mendapatkan **bobot yang adil** ($w_j$) untuk setiap kriteria penilaian.

📍 **Lokasi Logika Kode:** `app/Http/Controllers/BwmController.php` (fungsi `calculate()`).

**Cara Hitung di Sistem:**
Karena bahasa pemrograman web (PHP) pada dasarnya tidak memiliki pemecah masalah optimasi rumit (_linear/non-linear solver_) secara bawaan, sistem ini menggunakan **aproksimasi analitik BWM** yang sangat lazim dan andal digunakan di sistem web agar prosesnya cepat namun tetap akurat.

1. **Penentuan Best & Worst:** Pengguna memilih 1 Kriteria Terbaik (_Best_) dan 1 Terburuk (_Worst_).
2. **Matriks BO & OW:**
    - **BO (Best-to-Others):** Seberapa penting kriteria _Best_ dibandingkan kriteria lain (skala 1-9).
    - **OW (Others-to-Worst):** Seberapa penting kriteria lain dibandingkan kriteria _Worst_ (skala 1-9).
3. **Mencari Nilai Bobot ($w_j$):**
    - Berdasarkan teori, bobot berbanding terbalik dengan nilai BO. Sistem mencari bobot BO dengan rumus pendekatan: $w_{bo} = (1 / BO) / \Sigma (1 / BO)$
    - Sistem juga mencari bobot dari sisi OW dengan rumus: $w_{ow} = OW / \Sigma OW$
    - Bobot akhir adalah rata-rata dari keduanya: $w_j = (w_{bo} + w_{ow}) / 2$
4. **Normalisasi:** Memastikan total semua bobot jika dijumlahkan pasti = 1 (atau 100%). Hasil inilah yang disimpan ke database untuk dikirim ke fase TOPSIS.

### Fase 2: Perangkingan Nasabah dengan TOPSIS

Tujuan dari fase ini adalah untuk mencari nasabah mana yang paling dekat dengan "Kondisi Sangat Sempurna" dan paling jauh dari "Kondisi Sangat Buruk".

📍 **Lokasi Logika Kode:** `app/Http/Controllers/TopsisController.php` (fungsi `calculate()`).

**Langkah-langkah Hitung di Sistem:**

1. **Membentuk Matriks Keputusan (X):**
   Sistem mengambil semua nilai asesmen (_assessment_) dari database. Misal: Calon nasabah A punya nilai Penghasilan=5, Status Tempat Tinggal=3, dsb.
2. **Normalisasi Matriks (R):**
   Karena satuan dan rentang setiap kriteria berbeda-beda nilainya disamakan (_dinormalisasi_) dengan membagi setiap nilai dengan akar dari jumlah kuadrat seluruh nilai di kriteria tersebut.
3. **Pembobotan Matriks Normalisasi (V):**
   Di sinilah letak **integrasinya!** Sistem mengambil **bobot hasil dari BWM tadi** dan mengalikannya dengan matriks normalisasi ($v = r \times \text{bobot\_BWM}$).
4. **Menentukan Solusi Ideal Positif ($A^+$) dan Negatif ($A^-$):**
   Sistem mencari nilai maksimal dan minimal dari matriks V.
    - Jika kriteria **Benefit** (cth: Penghasilan, Uang Muka): $A^+$ adalah nilai terbesar, $A^-$ adalah nilai terkecil.
    - Jika kriteria **Cost** (cth: Jumlah Tanggungan): $A^+$ adalah nilai terkecil, $A^-$ adalah nilai terbesar.
5. **Menghitung Jarak ke Solusi Ideal ($D^+$ dan $D^-$):**
   Sistem mengukur seberapa jauh nilai masing-masing nasabah terhadap kondisi $A^+$ dan $A^-$ menggunakan rumus jarak _Euclidean_.
6. **Menghitung Nilai Preferensi / Kelayakan (CCi):**
   Ini adalah nilai metrik akhir (skala 0 sampai 1) dengan rumus: $CC_i = D^- / (D^+ + D^-)$. Semakin besar nilai $CC_i$ (mendekati 1), berarti sangat layak.
7. **Perangkingan Akhir:**
   Sistem mengurutkan seluruh calon nasabah dari nilai $CC_i$ terbesar ke terkecil dan menampilkannya di halaman Hasil TOPSIS.

---

## Listing Program (Core Algorithmic Logic for Paper)

Berikut adalah petikan kode logika utama (_listing program_) yang siap digunakan untuk penulisan karya ilmiah / skripsi / paper:

### 1. Listing Code BWM: Calculation of Criteria Weights

📍 **Source:** `app/Http/Controllers/BwmController.php`

```php
public function calculate(Request $request)
{
    $criterias = Criteria::all();
    if ($criterias->count() < 3) {
        return redirect()->back()->with('error', 'Minimal 3 kriteria diperlukan untuk BWM.');
    }

    // 1. Menentukan ID Kriteria Terbaik (Best) dan Terburuk (Worst)
    $best_id  = $request->best_criteria;
    $worst_id = $request->worst_criteria;

    $bo = $request->bo; // Vektor Best-to-Others (1-9)
    $ow = $request->ow; // Vektor Others-to-Worst (1-9)

    // Konsistensi dasar: Nilai Best terhadap dirinya sendiri = 1, dan Worst terhadap dirinya = 1
    $bo[$best_id]  = 1;
    $ow[$worst_id] = 1;

    // 2. Menghitung Pembagi Aproksimasi Analitik BWM
    $sum_inv_bo = 0;
    $sum_ow     = 0;

    foreach ($criterias as $c) {
        $sum_inv_bo += 1 / max(1, $bo[$c->id]);
        $sum_ow     += max(1, $ow[$c->id]);
    }

    $w_bo_list  = [];
    $w_ow_list  = [];
    $w_avg_list = [];

    // 3. Menghitung Bobot Parsial (Best-to-Others & Others-to-Worst)
    foreach ($criterias as $c) {
        // Bobot dari sudut pandang Kriteria Terbaik (BO)
        $w_bo_list[$c->id] = (1 / max(1, $bo[$c->id])) / $sum_inv_bo;

        // Bobot dari sudut pandang Kriteria Terburuk (OW)
        $w_ow_list[$c->id] = max(1, $ow[$c->id]) / $sum_ow;

        // Rata-rata bobot BO dan OW
        $w_avg_list[$c->id] = ($w_bo_list[$c->id] + $w_ow_list[$c->id]) / 2;
    }

    // 4. Normalisasi Bobot Akhir (Memastikan Total Bobot = 1 / 100%)
    $total_avg = array_sum($w_avg_list);

    foreach ($criterias as $c) {
        $final_weight = $w_avg_list[$c->id] / $total_avg;

        // Simpan bobot kriteria ke database
        $c->update(['weight' => $final_weight]);
    }

    // 5. Otomatis Menjalankan Ulang Kalkulasi TOPSIS setelah Bobot BWM Diperbarui
    app(TopsisController::class)->recalculateAll();

    return redirect()->route('bwm.index')->with('success', 'Bobot kriteria BWM berhasil diperbarui.');
}
```

### 2. Listing Code TOPSIS: Alternative Evaluation and Ranking

📍 **Source:** `app/Http/Controllers/TopsisController.php`

```php
public function recalculateAll()
{
    $criterias = Criteria::all();
    $customers = Customer::all();

    if ($criterias->isEmpty() || $customers->isEmpty()) {
        return false;
    }

    // 1. Membentuk Matriks Keputusan (X)
    $X = [];
    foreach ($customers as $customer) {
        foreach ($criterias as $criteria) {
            $val = Assessment::where('nasabah_id', $customer->id)
                             ->where('kriteria_id', $criteria->id)
                             ->value('value') ?? 0;
            $X[$customer->id][$criteria->id] = (float)$val;
        }
    }

    // 2. Normalisasi Matriks (R) Menggunakan Pembagi Vector Normalization
    $pembagi = [];
    foreach ($criterias as $criteria) {
        $sumSq = 0;
        foreach ($customers as $customer) {
            $sumSq += pow($X[$customer->id][$criteria->id], 2);
        }
        $pembagi[$criteria->id] = sqrt($sumSq);
    }

    $R = [];
    foreach ($customers as $customer) {
        foreach ($criterias as $criteria) {
            $val = $X[$customer->id][$criteria->id];
            $div = $pembagi[$criteria->id];
            $R[$customer->id][$criteria->id] = ($div == 0) ? 0 : ($val / $div);
        }
    }

    // 3. Pembobotan Matriks Normalisasi (V) Menggunakan Bobot BWM ($c->weight)
    $V = [];
    foreach ($customers as $customer) {
        foreach ($criterias as $criteria) {
            $V[$customer->id][$criteria->id] = $R[$customer->id][$criteria->id] * $criteria->weight;
        }
    }

    // 4. Menentukan Solusi Ideal Positif (A+) dan Solusi Ideal Negatif (A-)
    $A_plus = [];
    $A_min  = [];
    foreach ($criterias as $criteria) {
        $columnValues = array_column($V, $criteria->id);
        if ($criteria->type == 'benefit') {
            $A_plus[$criteria->id] = count($columnValues) > 0 ? max($columnValues) : 0;
            $A_min[$criteria->id]  = count($columnValues) > 0 ? min($columnValues) : 0;
        } else { // Jenis Kriteria Cost
            $A_plus[$criteria->id] = count($columnValues) > 0 ? min($columnValues) : 0;
            $A_min[$criteria->id]  = count($columnValues) > 0 ? max($columnValues) : 0;
        }
    }

    // 5. Menhitung Jarak Euclidean Solusi Ideal Positif (D+) dan Negatif (D-)
    $D_plus = [];
    $D_min  = [];
    foreach ($customers as $customer) {
        $sumDPlus = 0;
        $sumDMin  = 0;
        foreach ($criterias as $criteria) {
            $sumDPlus += pow($V[$customer->id][$criteria->id] - $A_plus[$criteria->id], 2);
            $sumDMin  += pow($V[$customer->id][$criteria->id] - $A_min[$criteria->id], 2);
        }
        $D_plus[$customer->id] = sqrt($sumDPlus);
        $D_min[$customer->id]  = sqrt($sumDMin);
    }

    // 6. Menghitung Nilai Preferensi Kelayakan (CCi)
    $CC = [];
    foreach ($customers as $customer) {
        $dp  = $D_plus[$customer->id];
        $dm  = $D_min[$customer->id];
        $cci = ($dp + $dm) == 0 ? 0 : ($dm / ($dp + $dm));
        $CC[$customer->id] = $cci;
    }

    // 7. Perankingan dan Pembaruan Data Skor & Peringkat Nasabah
    arsort($CC); // Urutkan dari nilai CCi tertinggi ke terendah
    $rank = 1;
    foreach ($CC as $custId => $score) {
        Customer::where('id', $custId)->update([
            'topsis_score' => $score,
            'topsis_rank'  => $rank
        ]);
        $rank++;
    }

    return true;
}
```

---

## Cara Instalasi & Menjalankan Sistem

1. Pastikan Anda memiliki PHP (rekomendasi >= 8.2) dan Composer terinstal.
2. Clone atau ekstrak repositori ini ke dalam direktori komputer Anda.
3. Buka terminal di dalam folder proyek, jalankan:
    ```bash
    composer install
    ```
4. Konfigurasi file `.env`. (Secara bawaan menggunakan `sqlite` untuk kemudahan).
5. Lakukan migrasi beserta _seeder_ untuk mengisi data dummy awal:
    ```bash
    php artisan migrate:fresh --seed
    ```
6. Jalankan server lokal:
    ```bash
    php artisan serve
    ```
7. Akses sistem di `http://localhost:8000`.

**Kredensial Login Default:**

- **Admin (Pengatur Bobot Indikator):** `admin` / `password`
- **MCE (Marketing Credit Executive):** `mce` / `password`
- **CAP (Credit Analyst & Approval Person):** `cap` / `password`

---

## Spesifikasi Tabel Database (Database Table Specifications)

Berikut adalah tabel spesifikasi teknis dari seluruh entitas basis data yang digunakan dalam sistem ini:

### 1. Spesifikasi Tabel `kriteria`

Tabel ini menyimpan data kriteria/indikator penilaian utama beserta bobot ($w_j$) hasil perhitungan BWM.

| Nama Field   | Tipe Data               | Key / Constraint            | Keterangan                                             |
| :----------- | :---------------------- | :-------------------------- | :----------------------------------------------------- |
| `id`         | BigInt (Unsigned)       | Primary Key, Auto Increment | Identifier unik kriteria                               |
| `code`       | Varchar(255)            | Unique, Not Null            | Kode Kriteria (C1, C2, C3, C4, C5)                     |
| `name`       | Varchar(255)            | Not Null                    | Nama Indikator Penilaian (Penghasilan, Pekerjaan, dll) |
| `type`       | Enum('benefit', 'cost') | Not Null                    | Sifat kriteria                                         |
| `weight`     | Double                  | Nullable                    | Bobot kriteria ($w_j$) dari BWM                        |
| `created_at` | Timestamp               | Nullable                    | Waktu pembuatan record                                 |
| `updated_at` | Timestamp               | Nullable                    | Waktu pembaruan record                                 |

### 2. Spesifikasi Tabel `subkriteria`

Tabel ini menyimpan rincian pilihan opsi/subkriteria untuk masing-masing kriteria beserta nilai skalanya (1-5).

| Nama Field    | Tipe Data         | Key / Constraint                            | Keterangan                      |
| :------------ | :---------------- | :------------------------------------------ | :------------------------------ |
| `id`          | BigInt (Unsigned) | Primary Key, Auto Increment                 | Identifier unik subkriteria     |
| `kriteria_id` | BigInt (Unsigned) | Foreign Key (`kriteria.id`), Cascade Delete | Merujuk ke Kriteria Induk       |
| `name`        | Varchar(255)      | Not Null                                    | Deskripsi pilihan subkriteria   |
| `score`       | Integer           | Not Null                                    | Skala nilai subkriteria (1 - 5) |
| `created_at`  | Timestamp         | Nullable                                    | Waktu pembuatan record          |
| `updated_at`  | Timestamp         | Nullable                                    | Waktu pembaruan record          |

### 3. Spesifikasi Tabel `nasabah`

Tabel ini menyimpan biodata calon nasabah, skor preferensi TOPSIS, peringkat, dan status persetujuan dari CAP.

| Nama Field           | Tipe Data                                | Key / Constraint            | Keterangan                              |
| :------------------- | :--------------------------------------- | :-------------------------- | :-------------------------------------- |
| `id`                 | BigInt (Unsigned)                        | Primary Key, Auto Increment | Identifier unik nasabah                 |
| `nik`                | Varchar(255)                             | Unique, Not Null            | NIK (Nomor Induk Kependudukan 16 digit) |
| `name`               | Varchar(255)                             | Not Null                    | Nama Lengkap Nasabah                    |
| `address`            | Varchar(255)                             | Not Null                    | Alamat Domisili Nasabah                 |
| `phone`              | Varchar(255)                             | Nullable                    | Nomor HP / WhatsApp Nasabah             |
| `topsis_score`       | Double                                   | Nullable                    | Skor Preferensi TOPSIS ($CC_i$)         |
| `topsis_rank`        | Integer                                  | Nullable                    | Peringkat Rekomendasi Kelayakan         |
| `status_persetujuan` | Enum('tertunda', 'disetujui', 'ditolak') | Default 'tertunda'          | Status Keputusan CAP                    |
| `created_at`         | Timestamp                                | Nullable                    | Waktu pembuatan record                  |
| `updated_at`         | Timestamp                                | Nullable                    | Waktu pembaruan record                  |

### 4. Spesifikasi Tabel `penilaian`

Tabel transaksi (_junction table_) yang menyimpan nilai evaluasi subkriteria setiap calon nasabah terhadap masing-masing kriteria (membentuk Matriks Keputusan $X_{ij}$).

| Nama Field    | Tipe Data         | Key / Constraint                            | Keterangan                                  |
| :------------ | :---------------- | :------------------------------------------ | :------------------------------------------ |
| `id`          | BigInt (Unsigned) | Primary Key, Auto Increment                 | Identifier unik penilaian                   |
| `nasabah_id`  | BigInt (Unsigned) | Foreign Key (`nasabah.id`), Cascade Delete  | Merujuk ke Nasabah                          |
| `kriteria_id` | BigInt (Unsigned) | Foreign Key (`kriteria.id`), Cascade Delete | Merujuk ke Kriteria Penilaian               |
| `value`       | Double            | Not Null                                    | Nilai skor subkriteria yang dipilih (1 - 5) |
| `created_at`  | Timestamp         | Nullable                                    | Waktu pembuatan record                      |
| `updated_at`  | Timestamp         | Nullable                                    | Waktu pembaruan record                      |

### 5. Spesifikasi Tabel `users`

Tabel ini menyimpan data akun pengguna aplikasi dan peran hak akses (_role-based access control_).

| Nama Field   | Tipe Data                   | Key / Constraint            | Keterangan                    |
| :----------- | :-------------------------- | :-------------------------- | :---------------------------- |
| `id`         | BigInt (Unsigned)           | Primary Key, Auto Increment | Identifier unik pengguna      |
| `name`       | Varchar(255)                | Not Null                    | Nama Lengkap Pengguna         |
| `username`   | Varchar(255)                | Unique, Not Null            | Username untuk Login          |
| `email`      | Varchar(255)                | Unique, Not Null            | Alamat Email Pengguna         |
| `password`   | Varchar(255)                | Not Null                    | Kata Sandi (Terenkripsi Hash) |
| `role`       | Enum('admin', 'mce', 'cap') | Not Null                    | Peran Hak Akses Pengguna      |
| `created_at` | Timestamp                   | Nullable                    | Waktu pembuatan record        |
| `updated_at` | Timestamp                   | Nullable                    | Waktu pembaruan record        |

---

## Aturan Bisnis (Rules) & PRD (Product Requirements Document)

Berdasarkan analisis _Activity Diagram_ dari sistem usulan, berikut adalah aturan alur kerja dan spesifikasi kebutuhan produk (_PRD_) yang dirancang untuk sistem ini:

### A. Aturan Bisnis (Business Rules) & Alur Kerja

#### Alur Utama (Happy Path)

1. **Otentikasi Akses:** Setiap pengguna wajib melakukan _login_ menggunakan username dan password yang valid. Sistem memvalidasi kredensial — jika **berhasil (Y)**, pengguna diarahkan ke _Dashboard Utama_.
2. **Pengajuan Calon Nasabah (MCE):** Aktor **MCE** memasukkan biodata calon nasabah serta memilih subkriteria indikator melalui formulir. Sistem memvalidasi kelengkapan data — jika **berhasil (Y)**, data disimpan ke _Database_ tabel `nasabah` dan `penilaian`.
3. **Pengelolaan & Perhitungan Kelayakan (Admin):** Aktor **Admin** mengatur perbandingan kriteria _Best_ dan _Worst_ (BWM), menjalankan perhitungan bobot BWM dan perangkingan TOPSIS.
4. **Persetujuan Final (CAP):** Aktor **CAP** meninjau pengajuan calon nasabah yang terdaftar sebagai _Layak_ atau _Sangat Layak_, kemudian melakukan persetujuan (_Approve_) atau penolakan (_Reject_).
5. **Pemantauan Hasil (MCE):** Aktor MCE dapat melihat status akhir kelayakan dan persetujuan dari CAP beserta penjelasan evaluasi detail nasabah.
