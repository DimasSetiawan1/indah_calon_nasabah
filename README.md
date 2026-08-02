# Sistem Rekomendasi Kelayakan Nasabah (BWM & TOPSIS)

Repositori ini memuat *source code* untuk skripsi dengan judul:
**"INTEGRASI BEST WORST METHOD (BWM) DAN TOPSIS DALAM MEREKOMENDASIKAN KELAYAKAN CALON NASABAH PADA PT FEDERAL INTERNASIONAL FINANCE"**

Sistem ini dibangun menggunakan *framework* web **Laravel 11** dan **Bootstrap CSS** untuk antarmuka yang bersih dan responsif.

---

## Alur Perhitungan Sistem (Logika Metode)

Sistem ini menggabungkan dua metode pengambilan keputusan multikriteria (MCDM), yaitu **BWM** untuk mencari bobot, dan **TOPSIS** untuk merangking alternatif. Berikut adalah bedah alur logika perhitungan di dalam kode programnya:

### Fase 1: Perhitungan Bobot Kriteria dengan BWM
Tujuan dari metode ini adalah untuk mendapatkan **bobot yang adil** ($w_j$) untuk setiap kriteria penilaian.

📍 **Lokasi Logika Kode:** `app/Http/Controllers/BwmController.php` (fungsi `calculate()`).

**Cara Hitung di Sistem:**
Karena bahasa pemrograman web (PHP) pada dasarnya tidak memiliki pemecah masalah optimasi rumit (*linear/non-linear solver*) secara bawaan, sistem ini menggunakan **aproksimasi analitik BWM** yang sangat lazim dan andal digunakan di sistem web agar prosesnya cepat namun tetap akurat.

1. **Penentuan Best & Worst:** Pengguna memilih 1 Kriteria Terbaik (*Best*) dan 1 Terburuk (*Worst*).
2. **Matriks BO & OW:**
   * **BO (Best-to-Others):** Seberapa penting kriteria *Best* dibandingkan kriteria lain (skala 1-9).
   * **OW (Others-to-Worst):** Seberapa penting kriteria lain dibandingkan kriteria *Worst* (skala 1-9).
3. **Mencari Nilai Bobot ($w_j$):**
   * Berdasarkan teori, bobot berbanding terbalik dengan nilai BO. Sistem mencari bobot BO dengan rumus pendekatan: $w_{bo} = (1 / BO) / \Sigma (1 / BO)$
   * Sistem juga mencari bobot dari sisi OW dengan rumus: $w_{ow} = OW / \Sigma OW$
   * Bobot akhir adalah rata-rata dari keduanya: $w_j = (w_{bo} + w_{ow}) / 2$
4. **Normalisasi:** Memastikan total semua bobot jika dijumlahkan pasti = 1 (atau 100%). Hasil inilah yang disimpan ke database untuk dikirim ke fase TOPSIS.

### Fase 2: Perangkingan Nasabah dengan TOPSIS
Tujuan dari fase ini adalah untuk mencari nasabah mana yang paling dekat dengan "Kondisi Sangat Sempurna" dan paling jauh dari "Kondisi Sangat Buruk".

📍 **Lokasi Logika Kode:** `app/Http/Controllers/TopsisController.php` (fungsi `calculate()`).

**Langkah-langkah Hitung di Sistem:**
1. **Membentuk Matriks Keputusan (X):**
   Sistem mengambil semua nilai asesmen (*assessment*) dari database. Misal: Calon nasabah A punya nilai Penghasilan=5, Status Tempat Tinggal=3, dsb.
2. **Normalisasi Matriks (R):**
   Karena satuan dan rentang setiap kriteria berbeda-beda nilainya disamakan (*dinormalisasi*) dengan membagi setiap nilai dengan akar dari jumlah kuadrat seluruh nilai di kriteria tersebut.
3. **Pembobotan Matriks Normalisasi (V):**
   Di sinilah letak **integrasinya!** Sistem mengambil **bobot hasil dari BWM tadi** dan mengalikannya dengan matriks normalisasi ($v = r \times \text{bobot\_BWM}$).
4. **Menentukan Solusi Ideal Positif ($A^+$) dan Negatif ($A^-$):**
   Sistem mencari nilai maksimal dan minimal dari matriks V.
   * Jika kriteria **Benefit** (cth: Penghasilan, Uang Muka): $A^+$ adalah nilai terbesar, $A^-$ adalah nilai terkecil.
   * Jika kriteria **Cost** (cth: Jumlah Tanggungan): $A^+$ adalah nilai terkecil, $A^-$ adalah nilai terbesar.
5. **Menghitung Jarak ke Solusi Ideal ($D^+$ dan $D^-$):**
   Sistem mengukur seberapa jauh nilai masing-masing nasabah terhadap kondisi $A^+$ dan $A^-$ menggunakan rumus jarak *Euclidean*.
6. **Menghitung Nilai Preferensi / Kelayakan (CCi):**
   Ini adalah nilai metrik akhir (skala 0 sampai 1) dengan rumus: $CC_i = D^- / (D^+ + D^-)$. Semakin besar nilai $CC_i$ (mendekati 1), berarti sangat layak.
7. **Perangkingan Akhir:**
   Sistem mengurutkan seluruh calon nasabah dari nilai $CC_i$ terbesar ke terkecil dan menampilkannya di halaman Hasil TOPSIS.

---

## Cara Instalasi & Menjalankan Sistem

1. Pastikan Anda memiliki PHP (rekomendasi >= 8.2) dan Composer terinstal.
2. Clone atau ekstrak repositori ini ke dalam direktori komputer Anda.
3. Buka terminal di dalam folder proyek, jalankan:
   ```bash
   composer install
   ```
4. Konfigurasi file `.env`. (Secara bawaan menggunakan `sqlite` untuk kemudahan).
5. Lakukan migrasi beserta *seeder* untuk mengisi data dummy awal:
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

| Nama Field | Tipe Data | Key / Constraint | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | BigInt (Unsigned) | Primary Key, Auto Increment | Identifier unik kriteria |
| `code` | Varchar(255) | Unique, Not Null | Kode Kriteria (C1, C2, C3, C4, C5) |
| `name` | Varchar(255) | Not Null | Nama Indikator Penilaian (Penghasilan, Pekerjaan, dll) |
| `type` | Enum('benefit', 'cost') | Not Null | Sifat kriteria |
| `weight` | Double | Nullable | Bobot kriteria ($w_j$) dari BWM |
| `created_at` | Timestamp | Nullable | Waktu pembuatan record |
| `updated_at` | Timestamp | Nullable | Waktu pembaruan record |

### 2. Spesifikasi Tabel `subkriteria`
Tabel ini menyimpan rincian pilihan opsi/subkriteria untuk masing-masing kriteria beserta nilai skalanya (1-5).

| Nama Field | Tipe Data | Key / Constraint | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | BigInt (Unsigned) | Primary Key, Auto Increment | Identifier unik subkriteria |
| `kriteria_id` | BigInt (Unsigned) | Foreign Key (`kriteria.id`), Cascade Delete | Merujuk ke Kriteria Induk |
| `name` | Varchar(255) | Not Null | Deskripsi pilihan subkriteria |
| `score` | Integer | Not Null | Skala nilai subkriteria (1 - 5) |
| `created_at` | Timestamp | Nullable | Waktu pembuatan record |
| `updated_at` | Timestamp | Nullable | Waktu pembaruan record |

### 3. Spesifikasi Tabel `nasabah`
Tabel ini menyimpan biodata calon nasabah, skor preferensi TOPSIS, peringkat, dan status persetujuan dari CAP.

| Nama Field | Tipe Data | Key / Constraint | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | BigInt (Unsigned) | Primary Key, Auto Increment | Identifier unik nasabah |
| `nik` | Varchar(255) | Unique, Not Null | NIK (Nomor Induk Kependudukan 16 digit) |
| `name` | Varchar(255) | Not Null | Nama Lengkap Nasabah |
| `address` | Varchar(255) | Not Null | Alamat Domisili Nasabah |
| `phone` | Varchar(255) | Nullable | Nomor HP / WhatsApp Nasabah |
| `topsis_score` | Double | Nullable | Skor Preferensi TOPSIS ($CC_i$) |
| `topsis_rank` | Integer | Nullable | Peringkat Rekomendasi Kelayakan |
| `status_persetujuan` | Enum('tertunda', 'disetujui', 'ditolak') | Default 'tertunda' | Status Keputusan CAP |
| `created_at` | Timestamp | Nullable | Waktu pembuatan record |
| `updated_at` | Timestamp | Nullable | Waktu pembaruan record |

### 4. Spesifikasi Tabel `penilaian`
Tabel transaksi (*junction table*) yang menyimpan nilai evaluasi subkriteria setiap calon nasabah terhadap masing-masing kriteria (membentuk Matriks Keputusan $X_{ij}$).

| Nama Field | Tipe Data | Key / Constraint | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | BigInt (Unsigned) | Primary Key, Auto Increment | Identifier unik penilaian |
| `nasabah_id` | BigInt (Unsigned) | Foreign Key (`nasabah.id`), Cascade Delete | Merujuk ke Nasabah |
| `kriteria_id` | BigInt (Unsigned) | Foreign Key (`kriteria.id`), Cascade Delete | Merujuk ke Kriteria Penilaian |
| `value` | Double | Not Null | Nilai skor subkriteria yang dipilih (1 - 5) |
| `created_at` | Timestamp | Nullable | Waktu pembuatan record |
| `updated_at` | Timestamp | Nullable | Waktu pembaruan record |

### 5. Spesifikasi Tabel `users`
Tabel ini menyimpan data akun pengguna aplikasi dan peran hak akses (*role-based access control*).

| Nama Field | Tipe Data | Key / Constraint | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | BigInt (Unsigned) | Primary Key, Auto Increment | Identifier unik pengguna |
| `name` | Varchar(255) | Not Null | Nama Lengkap Pengguna |
| `username` | Varchar(255) | Unique, Not Null | Username untuk Login |
| `email` | Varchar(255) | Unique, Not Null | Alamat Email Pengguna |
| `password` | Varchar(255) | Not Null | Kata Sandi (Terenkripsi Hash) |
| `role` | Enum('admin', 'mce', 'cap') | Not Null | Peran Hak Akses Pengguna |
| `created_at` | Timestamp | Nullable | Waktu pembuatan record |
| `updated_at` | Timestamp | Nullable | Waktu pembaruan record |

---

## Aturan Bisnis (Rules) & PRD (Product Requirements Document)

Berdasarkan analisis *Activity Diagram* dari sistem usulan, berikut adalah aturan alur kerja dan spesifikasi kebutuhan produk (*PRD*) yang dirancang untuk sistem ini:

### A. Aturan Bisnis (Business Rules) & Alur Kerja

#### Alur Utama (Happy Path)
1. **Otentikasi Akses:** Setiap pengguna wajib melakukan *login* menggunakan username dan password yang valid. Sistem memvalidasi kredensial — jika **berhasil (Y)**, pengguna diarahkan ke *Dashboard Utama*.
2. **Pengajuan Calon Nasabah (MCE):** Aktor **MCE** memasukkan biodata calon nasabah serta memilih subkriteria indikator melalui formulir. Sistem memvalidasi kelengkapan data — jika **berhasil (Y)**, data disimpan ke *Database* tabel `nasabah` dan `penilaian`.
3. **Pengelolaan & Perhitungan Kelayakan (Admin):** Aktor **Admin** mengatur perbandingan kriteria *Best* dan *Worst* (BWM), menjalankan perhitungan bobot BWM dan perangkingan TOPSIS.
4. **Persetujuan Final (CAP):** Aktor **CAP** meninjau pengajuan calon nasabah yang terdaftar sebagai *Layak* atau *Sangat Layak*, kemudian melakukan persetujuan (*Approve*) atau penolakan (*Reject*).
5. **Pemantauan Hasil (MCE):** Aktor MCE dapat melihat status akhir kelayakan dan persetujuan dari CAP beserta penjelasan evaluasi detail nasabah.
