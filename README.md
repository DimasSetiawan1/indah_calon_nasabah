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
- **Email:** `admin@fif.com`
- **Password:** `password`

---

## Struktur Database & Relasi (Class Diagram)

Sistem ini memiliki 4 entitas utama dalam basis data. Berikut adalah detail struktur tabel dan relasinya yang dapat digunakan sebagai acuan untuk membuat *Class Diagram* atau *Entity Relationship Diagram* (ERD):

### 1. Tabel `users`
Tabel ini digunakan untuk autentikasi dan manajemen akses admin.
* `id` (PK, BigInt)
* `name` (String)
* `email` (String, Unique)
* `email_verified_at` (Timestamp, Nullable)
* `password` (String)
* `remember_token` (String, Nullable)
* `timestamps` (created_at, updated_at)
* **Relasi:** Tidak memiliki relasi langsung ke tabel transaksi/master lainnya.

### 2. Tabel `criterias`
Menyimpan data kriteria penilaian beserta bobot (hasil perhitungan BWM).
* `id` (PK, BigInt)
* `code` (String, Unique) - *Contoh: C1, C2*
* `name` (String) - *Contoh: Penghasilan, Usia*
* `type` (Enum: 'benefit', 'cost')
* `weight` (Double, Nullable) - *Nilai bobot prioritas kriteria dari BWM*
* `timestamps` (created_at, updated_at)
* **Relasi:** *One-to-Many* (`hasMany`) ke tabel `assessments`. (Satu kriteria bisa memiliki banyak nilai asesmen).

### 3. Tabel `customers`
Menyimpan biodata calon nasabah beserta hasil akhir dari perhitungan TOPSIS.
* `id` (PK, BigInt)
* `nik` (String, Unique)
* `name` (String)
* `address` (String)
* `phone` (String, Nullable)
* `topsis_score` (Double, Nullable) - *Nilai akhir (Preferensi CCi)*
* `topsis_rank` (Integer, Nullable) - *Hasil peringkat nasabah*
* `timestamps` (created_at, updated_at)
* **Relasi:** *One-to-Many* (`hasMany`) ke tabel `assessments`. (Satu nasabah memiliki banyak nilai asesmen sesuai jumlah kriteria).

### 4. Tabel `assessments`
Tabel transaksi (atau *pivot*) yang menyimpan penilaian (skor) aktual setiap nasabah pada setiap kriteria.
* `id` (PK, BigInt)
* `customer_id` (FK, BigInt) -> *Merujuk ke tabel `customers`*
* `criteria_id` (FK, BigInt) -> *Merujuk ke tabel `criterias`*
* `value` (Double) - *Nilai asesmen nasabah pada kriteria tersebut*
* `timestamps` (created_at, updated_at)
* **Relasi:**
  * *Many-to-One* (`belongsTo`) ke tabel `customers`.
  * *Many-to-One* (`belongsTo`) ke tabel `criterias`.

### Ringkasan Relasi Utama
Pusat relasi berada pada tabel `assessments` yang menjembatani antara nasabah dan kriteria:
**Customer** `(1) ------- (M)` **Assessment** `(M) ------- (1)` **Criteria**
