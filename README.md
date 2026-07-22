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

### 1. Tabel `pengguna`
Tabel ini digunakan untuk autentikasi dan manajemen akses admin.
* `id` (PK, BigInt)
* `name` (String)
* `email` (String, Unique)
* `email_verified_at` (Timestamp, Nullable)
* `password` (String)
* `remember_token` (String, Nullable)
* `timestamps` (created_at, updated_at)
* **Relasi:** Tidak memiliki relasi langsung ke tabel transaksi/master lainnya.

### 2. Tabel `kriteria`
Menyimpan data kriteria penilaian beserta bobot (hasil perhitungan BWM).
* `id` (PK, BigInt)
* `code` (String, Unique) - *Contoh: C1, C2*
* `name` (String) - *Contoh: Penghasilan, Usia*
* `type` (Enum: 'benefit', 'cost')
* `weight` (Double, Nullable) - *Nilai bobot prioritas kriteria dari BWM*
* `timestamps` (created_at, updated_at)
* **Relasi:** *One-to-Many* (`hasMany`) ke tabel `penilaian`. (Satu kriteria bisa memiliki banyak nilai penilaian).

### 3. Tabel `nasabah`
Menyimpan biodata calon nasabah beserta hasil akhir dari perhitungan TOPSIS.
* `id` (PK, BigInt)
* `nik` (String, Unique)
* `name` (String)
* `address` (String)
* `phone` (String, Nullable)
* `topsis_score` (Double, Nullable) - *Nilai akhir (Preferensi CCi)*
* `topsis_rank` (Integer, Nullable) - *Hasil peringkat nasabah*
* `timestamps` (created_at, updated_at)
* **Relasi:** *One-to-Many* (`hasMany`) ke tabel `penilaian` dan `surat_kontrak`.

### 4. Tabel `penilaian`
Tabel transaksi (*pivot*) yang menyimpan penilaian (skor) aktual setiap nasabah pada setiap kriteria.
* `id` (PK, BigInt)
* `nasabah_id` (FK, BigInt) -> *Merujuk ke tabel `nasabah`*
* `kriteria_id` (FK, BigInt) -> *Merujuk ke tabel `kriteria`*
* `value` (Double) - *Nilai penilaian nasabah pada kriteria tersebut*
* `timestamps` (created_at, updated_at)
* **Relasi:**
  * *Many-to-One* (`belongsTo`) ke tabel `nasabah`.
  * *Many-to-One* (`belongsTo`) ke tabel `kriteria`.

### 5. Tabel `surat_kontrak` *(BARU)*
Menyimpan data surat kontrak yang diterbitkan setelah persetujuan berjenjang (Admin → CAP).
* `id` (PK, BigInt)
* `nasabah_id` (FK, BigInt) -> *Merujuk ke tabel `nasabah`*
* `nomor_kontrak` (String, Unique) - *Nomor kontrak yang di-generate otomatis*
* `status` (Enum: 'disetujui_admin', 'disetujui_cap', 'aktif', 'selesai')
* `tanggal_kontrak` (Date, Nullable)
* `keterangan` (Text, Nullable)
* `timestamps` (created_at, updated_at)
* **Relasi:** *Many-to-One* (`belongsTo`) ke tabel `nasabah`.

### Ringkasan Relasi Utama
Pusat relasi berada pada tabel `penilaian` yang menjembatani antara nasabah dan kriteria:

**`nasabah`** `(1) ──── (M)` **`penilaian`** `(M) ──── (1)` **`kriteria`**

**`nasabah`** `(1) ──── (M)` **`surat_kontrak`**

---

## Aturan Bisnis (Rules) & PRD (Product Requirements Document)

Berdasarkan analisis *Activity Diagram* dari sistem usulan, berikut adalah aturan alur kerja dan spesifikasi kebutuhan produk (*PRD*) yang dirancang untuk sistem ini:

### A. Aturan Bisnis (Business Rules) & Alur Kerja

#### Alur Utama (Happy Path)
1. **Otentikasi Akses:** Setiap pengguna wajib melakukan *login* menggunakan nama pengguna dan kata sandi yang valid. Sistem memvalidasi kredensial — jika **berhasil (Y)**, pengguna diarahkan ke *Dashboard Utama*.
2. **Pengajuan Calon Nasabah (MCE):** Aktor **MCE** memasukkan biodata calon nasabah melalui formulir. Sistem memvalidasi kelengkapan data — jika **berhasil (Y)**, data disimpan ke *Database* tabel `Nasabah` dan masuk ke *Daftar Calon Nasabah*.
3. **Pengelolaan & Perhitungan Kelayakan (Admin):** Aktor **Admin** membuka *Daftar Calon Nasabah*, memiliki wewenang penuh untuk menambah (input), mengubah, atau menghapus nasabah, melakukan pengaturan kriteria *Best* dan *Worst* (BWM), menjalankan perhitungan TOPSIS, dan memvalidasi hasil. Jika Admin **menyetujui (Y)**, nasabah dipindahkan ke *Daftar Nasabah Disetujui*.
4. **Persetujuan Final & Kontrak (CAP):** Aktor **CAP** menerima *Daftar Nasabah Disetujui* dari Admin, kemudian melakukan persetujuan akhir dan **membuat Surat Kontrak**.
5. **Pencatatan Kontrak (Sistem):** Sistem meng-*generate* **Nomor Surat Kontrak** secara otomatis dan menyimpan data surat kontrak ke *Database* (tabel `Surat Kontrak`).
6. **Informasi Pencairan (MCE):** Sistem menampilkan **Informasi Pencairan Dana Nasabah**. Aktor MCE menerima notifikasi bahwa dana untuk nasabah tersebut telah cair, dan siklus pengajuan selesai.

#### Alur Alternatif (Skenario Gagal / Penolakan)
* **Validasi Login GAGAL (T):** Jika kredensial salah, sistem menolak akses dan MCE harus menginput ulang nama pengguna dan kata sandi.
* **Validasi Data Nasabah GAGAL (T):** Jika data calon nasabah tidak lolos validasi (tidak lengkap/tidak sesuai format), sistem mengembalikan MCE ke halaman formulir input untuk diperbaiki. Data **tidak** masuk ke daftar calon nasabah.
* **Admin Menolak (T) / Validasi Admin GAGAL:** Jika Admin menilai calon nasabah tidak layak setelah melihat hasil perhitungan, Admin **tidak** menyetujui pengajuan sehingga nasabah **tidak** diteruskan ke antrian CAP.

### B. Product Requirements Document (PRD)

#### 1. Tujuan Produk
Membangun sistem terpusat untuk mengotomatisasi proses pengajuan nasabah, penilaian kelayakan secara objektif (BWM & TOPSIS), persetujuan berjenjang, hingga penerbitan surat kontrak dan notifikasi pencairan dana.

#### 2. Pengguna Sistem (Aktor / Role)
* **MCE (Marketing Credit Executive):** Pengguna yang mencari dan mendaftarkan calon nasabah dari lapangan.
* **Admin:** Pihak yang bertanggung jawab penuh atas pengelolaan perhitungan kelayakan (menentukan kriteria *Best* dan *Worst*, menjalankan BWM-TOPSIS), melakukan verifikasi data, dan memberikan persetujuan tahap pertama.
* **CAP (Credit Analyst / Approval Person):** Pihak pengambil keputusan akhir yang memberikan *Final Approval* dan menerbitkan surat kontrak perjanjian.

#### 3. Kebutuhan Fitur (Features)

* **Modul Autentikasi (Multi-role):**
  * Halaman Login terpusat untuk semua aktor (MCE, Admin, CAP).
  * Validasi kredensial — tampilkan pesan error dan kembali ke form login jika gagal.
  * Pembatasan hak akses (*middleware*) dan pengalihan ke *dashboard* masing-masing peran setelah login berhasil.

* **Modul Input & Pelacakan Nasabah (Akses: MCE):**
  * Form pengisian biodata calon nasabah (NIK, Nama, Alamat, Telepon, dan nilai per kriteria).
  * Validasi *form* sisi server dan klien — tampilkan pesan error spesifik jika data tidak valid.
  * Halaman **Informasi Dana Nasabah Cair**: menampilkan notifikasi/status akhir bahwa pengajuan telah selesai dan dana nasabah telah dicairkan.

* **Modul Pengelolaan & Perhitungan Kelayakan (Akses: Admin):**
  * Halaman **Daftar Calon Nasabah**: menampilkan seluruh pengajuan MCE yang masuk beserta data nilainya.
  * Halaman **Pengaturan Kriteria (BWM)**: form untuk menentukan kriteria *Best* (terbaik) dan *Worst* (terburuk), serta memasukkan nilai perbandingan BO dan OW.
  * Fitur **Hitung Bobot BWM**: tombol eksekusi untuk menghitung dan menyimpan bobot ($w_j$) ke database.
  * Fitur **Hitung TOPSIS**: tombol eksekusi untuk menjalankan perangkingan seluruh calon nasabah berdasarkan bobot BWM.
  * Tampilan hasil skor TOPSIS ($CC_i$) dan peringkat untuk setiap calon nasabah.
  * Tombol **Setuju (Approve)** untuk memindahkan nasabah yang layak ke *Daftar Nasabah Disetujui* dan meneruskannya ke CAP.

* **Modul Approval & Pembuatan Kontrak (Akses: CAP):**
  * Halaman **Daftar Nasabah Disetujui**: menampilkan nasabah yang telah disetujui Admin (terpisah dari daftar calon).
  * Tombol **Setuju Final (Final Approve)** sebagai persetujuan akhir oleh CAP.
  * Fitur **Buat Surat Kontrak**: form atau *generate* otomatis surat kontrak untuk nasabah yang disetujui.

* **Modul Arsip Kontrak & Database:**
  * *Generate* Nomor Surat Kontrak secara otomatis dan sistematis.
  * Penyimpanan data Surat Kontrak di tabel `surat_kontrak` dalam *Database*.
  * Sistem memperbarui status pencairan nasabah sehingga informasi dapat ditampilkan ke MCE.

* **Entitas Database yang Dibutuhkan (sesuai diagram):**
  * Tabel `MCE` / `users` — data akun pengguna (MCE, Admin, CAP).
  * Tabel `customers` (Nasabah) — biodata dan hasil TOPSIS calon nasabah.
  * Tabel `surat_kontrak` — nomor dan detail kontrak yang dibuat oleh CAP.
  * Tabel `criterias` — kriteria penilaian beserta bobot BWM.
  * Tabel `assessments` — nilai per kriteria untuk tiap calon nasabah.
