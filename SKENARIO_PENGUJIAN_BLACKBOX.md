# Dokumen Skenario Pengujian Black Box (Black Box Testing Scenario)
## Sistem Rekomendasi Penilaian Kelayakan Calon Nasabah PT FIFGROUP

---

### **1. Pendahuluan**
Dokumen ini berisi rancangan skenario pengujian *Black Box* untuk **Sistem Rekomendasi Penilaian Kelayakan Calon Nasabah PT FIFGROUP** yang diintegrasikan menggunakan metode *Best Worst Method* (BWM) dan *TOPSIS*. Pengujian ini bertujuan untuk memastikan seluruh fitur, hak akses (*Role-Based Access Control*), validasi input, serta alur kerja (*workflow*) aplikasi berjalan sesuai dengan spesifikasi kebutuhan pengguna (*User Requirements*).

---

### **2. Lingkup Pengujian & Aktor (Role)**
Aplikasi ini memiliki 3 aktor/role utama dengan batasan hak akses sebagai berikut:
1. **Admin**: Mengatur bobot indikator penilaian kelayakan dan melihat hasil perangkingan.
2. **MCE (Marketing Credit Executive)**: Menginputkan & mengedit data calon nasabah beserta indikator kelayakan, melihat hasil perangkingan, serta memantau status persetujuan.
3. **CAP (Credit Approval Process)**: Meninjau hasil kelayakan nasabah dan mengeksekusi persetujuan (*Setujui* / *Tolak*).

---

### **3. Matriks Skenario Pengujian Black Box**

#### **A. Modul Authentikasi & Keamanan Akses (Login & Logout)**

| ID Test | Fitur / Fungsi | Skenario Pengujian | Input Data | Hasil yang Diharapkan | Kategori Hasil |
| :--- | :--- | :--- | :--- | :--- | :---: |
| **TC-AUTH-01** | Login Valid | Menguji login pengguna terdaftar dengan role Admin, MCE, atau CAP | - Username: `admin` / `mce` / `cap`<br>- Password: `password` | Sistem berhasil memverifikasi akun dan mengarahkan ke halaman Dashboard sesuai role. | **BERHASIL (PASS)** |
| **TC-AUTH-02** | Login Username Salah | Menguji login dengan username yang tidak terdaftar | - Username: `user_salah`<br>- Password: `password` | Sistem menolak login dan menampilkan pesan kesalahan: *"Username atau password salah."* | **BERHASIL (PASS)** |
| **TC-AUTH-03** | Login Password Salah | Menguji login dengan password yang tidak sesuai | - Username: `admin`<br>- Password: `passwordsalah` | Sistem menolak login dan menampilkan pesan kesalahan: *"Username atau password salah."* | **BERHASIL (PASS)** |
| **TC-AUTH-04** | Login Form Kosong | Menguji submit form login tanpa mengisi username & password | - Username: *(kosong)*<br>- Password: *(kosong)* | HTML5 validation mencegah form dikirim dan meminta pengguna mengisi bidang wajib. | **BERHASIL (PASS)** |
| **TC-AUTH-05** | Logout | Menguji keluar dari sesi aplikasi | Klik tombol *"Keluar"* pada topbar | Sesi dihapus, pengguna berhasil di-redirect kembali ke halaman Login. | **BERHASIL (PASS)** |
| **TC-AUTH-06** | Access Control (Guest) | Mengakses URL internal langsung tanpa login | Akses langsung ke URL `http://localhost/dashboard` atau `/customers` | Sistem memblokir akses dan mengarahkan pengguna ke halaman Login secara otomatis. | **BERHASIL (PASS)** |

---

#### **B. Modul Data Calon Nasabah (Khusus Role MCE)**

| ID Test | Fitur / Fungsi | Skenario Pengujian | Input Data | Hasil yang Diharapkan | Kategori Hasil |
| :--- | :--- | :--- | :--- | :--- | :---: |
| **TC-CUST-01** | Tambah Nasabah Valid | Menginputkan data pribadi calon nasabah beserta 5 indikator kelayakan lengkap | - NIK: `3175012345670001`<br>- Nama: `Ahmad Subagja`<br>- No HP: `08123456789`<br>- Alamat: `Jl. Melati No. 12`<br>- Dropdown 5 Indikator: Terisi semua | Sistem menyimpan data nasabah & nilai indikator ke database, lalu menampilkan notifikasi sukses. | **BERHASIL (PASS)** |
| **TC-CUST-02** | Validasi NIK Duplikat | Menginputkan NIK yang sudah terdaftar di database | - NIK: `3175012345670001` *(Sudah ada)*<br>- Data lain: Valid | Sistem menolak form dan menampilkan pesan error: *"NIK ini sudah terdaftar."* | **BERHASIL (PASS)** |
| **TC-CUST-03** | Validasi Format NIK | Menginputkan NIK yang bukan 16 digit atau memuat karakter non-angka | - NIK: `12345` / `317501ABCD670001` | Sistem menolak simpan dan menampilkan pesan error: *"NIK wajib 16 digit"* / *"NIK harus berupa angka."* | **BERHASIL (PASS)** |
| **TC-CUST-04** | Validasi Nama | Menginputkan nama nasabah menggunakan karakter angka atau spesial | - Nama: `Ahmad 123 !@#` | Sistem menolak simpan dan menampilkan pesan error: *"Nama tidak boleh mengandung angka atau karakter spesial."* | **BERHASIL (PASS)** |
| **TC-CUST-05** | Validasi Dropdown Indikator | Mengosongkan salah satu atau seluruh pilihan dropdown indikator | - Dropdown Indikator: `-- Pilih Indikator --` | Form menolak disubmit dan mewajibkan pengguna memilih indikator kelayakan. | **BERHASIL (PASS)** |
| **TC-CUST-06** | Edit Nasabah | Memperbarui data pribadi dan penyesuaian pilihan indikator kelayakan | Mengubah Alamat & Pilihan Indikator Pekerjaan pada nasabah terpilih | Data nasabah & nilai penilaian diperbarui di database, sistem menampilkan notifikasi sukses. | **BERHASIL (PASS)** |
| **TC-CUST-07** | Hapus Nasabah | Menghapus data calon nasabah dari tabel daftar nasabah | Klik tombol *"Hapus"* & konfirmasi hapus | Data nasabah beserta record penilaian terkait terhapus dari sistem. | **BERHASIL (PASS)** |

---

#### **C. Modul Pengaturan Bobot Penilaian (Khusus Role Admin)**

| ID Test | Fitur / Fungsi | Skenario Pengujian | Input Data | Hasil yang Diharapkan | Kategori Hasil |
| :--- | :--- | :--- | :--- | :--- | :---: |
| **TC-BWM-01** | Input Preferensi Valid | Memilih Indikator Utama & Pendukung serta mengisi nilai skala perbandingan (1-9) | - Utama: `Penghasilan`<br>- Pendukung: `Lama Tempat Tinggal`<br>- Nilai Matriks: Skala 1 s.d 9 | Sistem menghitung bobot indikator secara presisi, meng-update bobot ke database, dan menghitung ulang skor TOPSIS seluruh nasabah. | **BERHASIL (PASS)** |
| **TC-BWM-02** | Penguncian Skala Otomatis | Memeriksa fungsi penguncian otomatis (*read-only*) pada perbandingan indikator terpenting vs terpenting | Pilih Indikator Utama pada dropdown | Input perbandingan antara indikator utama terhadap dirinya sendiri otomatis terisi angka `1` dan di-lock. | **BERHASIL (PASS)** |
| **TC-BWM-03** | Reset & Re-Calculate | Mengubah ulang preferensi indikator utama dan menghitung kembali | Mengubah Utama ke `Riwayat Kredit` dan hitung ulang | Bobot indikator baru berhasil diperbarui dan diterapkan ke seluruh penilaian nasabah. | **BERHASIL (PASS)** |

---

#### **D. Modul Hasil Perangkingan Kelayakan (Admin, MCE, CAP)**

| ID Test | Fitur / Fungsi | Skenario Pengujian | Input Data | Hasil yang Diharapkan | Kategori Hasil |
| :--- | :--- | :--- | :--- | :--- | :---: |
| **TC-RANK-01** | Urutan Perangkingan | Memeriksa tabel perangkingan nasabah berdasarkan hasil perhitungan | Klik menu *"Hasil Perangkingan"* | Nasabah ditampilkan terurut dari Peringkat 1 (Skor Kelayakan terbesar) hingga terkecil. | **BERHASIL (PASS)** |
| **TC-RANK-02** | Label Kelayakan | Memeriksa pengkategorian status kelayakan berdasarkan peringkat & skor | - Rank 1-3: *Sangat Layak*<br>- Skor >= 0.5: *Layak*<br>- Skor < 0.5: *Tidak Layak* | Badge warna pada kolom Status Kelayakan sesuai dengan kriteria yang ditentukan. | **BERHASIL (PASS)** |
| **TC-RANK-03** | Handling Data Kosong | Mengakses halaman Hasil Perangkingan saat bobot / data nasabah belum diisi | Database nasabah/bobot kosong | Menampilkan alert peringatan bahwa perhitungan belum dapat dilakukan. | **BERHASIL (PASS)** |

---

#### **E. Modul Persetujuan / Approval (POV CAP & MCE)**

| ID Test | Fitur / Fungsi | Skenario Pengujian | Input Data | Hasil yang Diharapkan | Kategori Hasil |
| :--- | :--- | :--- | :--- | :--- | :---: |
| **TC-APP-01** | Akses Filter Persetujuan | Membuka halaman persetujuan dari sisi CAP / MCE | Klik menu *"Persetujuan"* | Hanya menampilkan calon nasabah yang masuk kategori **LAYAK** saja (Tidak Layak disembunyikan). | **BERHASIL (PASS)** |
| **TC-APP-02** | Eksekusi Setujui (CAP) | CAP menekan tombol *"Setujui"* pada pengajuan nasabah berstatus Tertunda | Klik tombol *"Setujui"* & konfirmasi dialog | Status persetujuan nasabah berubah menjadi **Disetujui** (badge hijau terkunci). | **BERHASIL (PASS)** |
| **TC-APP-03** | Eksekusi Tolak (CAP) | CAP menekan tombol *"Tolak"* pada pengajuan nasabah berstatus Tertunda | Klik tombol *"Tolak"* & konfirmasi dialog | Status persetujuan nasabah berubah menjadi **Ditolak** (badge merah terkunci). | **BERHASIL (PASS)** |
| **TC-APP-04** | Immutability Status | Mencoba mengubah status nasabah yang sudah Disetujui atau Ditolak | Melihat baris nasabah yang sudah diproses | Tombol Setujui/Tolak hilang dan digantikan oleh *badge lock status* (Status tidak dapat diubah lagi). | **BERHASIL (PASS)** |
| **TC-APP-05** | Monitoring MCE | MCE melihat hasil persetujuan yang dilakukan oleh CAP | Log in sebagai role `MCE` -> Buka menu Persetujuan | MCE dapat melihat badge status persetujuan (*Disetujui/Ditolak/Tertunda*) secara transparan tanpa tombol aksi eksekusi. | **BERHASIL (PASS)** |

---

#### **F. Modul Pembatasan Hak Akses Menu (Role-Based Access Control)**

| ID Test | Fitur / Fungsi | Skenario Pengujian | Role Aktor | Hasil yang Diharapkan | Kategori Hasil |
| :--- | :--- | :--- | :--- | :--- | :---: |
| **TC-RBAC-01** | Pembatasan Menu Admin | Memeriksa ketersediaan menu di sidebar Admin | Role: `Admin` | Hanya menampilkan menu: **Dasbor**, **Bobot Penilaian**, **Hasil Perangkingan**. (Menu Nasabah & Persetujuan tersembunyi). | **BERHASIL (PASS)** |
| **TC-RBAC-02** | Pembatasan Menu MCE | Memeriksa ketersediaan menu di sidebar MCE | Role: `MCE` | Tampil menu: **Dasbor**, **Calon Nasabah**, **Hasil Perangkingan**, **Persetujuan**. (Menu Bobot Penilaian tersembunyi). | **BERHASIL (PASS)** |
| **TC-RBAC-03** | Pembatasan Menu CAP | Memeriksa ketersediaan menu di sidebar CAP | Role: `CAP` | Tampil menu: **Dasbor**, **Hasil Perangkingan**, **Persetujuan**. (Menu Nasabah & Bobot Penilaian tersembunyi). | **BERHASIL (PASS)** |
| **TC-RBAC-04** | Proteksi URL MCE -> BWM | MCE mencoba mengakses URL `/bwm` secara manual via browser address bar | Role: `MCE` | Sistem memblokir akses (HTTP 403 Forbidden / Redirect ke Dashboard). | **BERHASIL (PASS)** |
| **TC-RBAC-05** | Proteksi URL Admin -> Nasabah | Admin mencoba mengakses URL `/customers` secara manual via browser address bar | Role: `Admin` | Sistem memblokir akses (HTTP 403 Forbidden / Redirect ke Dashboard). | **BERHASIL (PASS)** |

---

### **4. Kesimpulan Hasil Pengujian**
Berdasarkan seluruh skenario pengujian *Black Box* yang telah dirancang dan dieksekusi di atas, seluruh fitur aplikasi **Sistem Rekomendasi Penilaian Kelayakan Calon Nasabah PT FIFGROUP** dinyatakan **BERHASIL (100% PASS)** dan telah memenuhi kriteria kelayakan sistem yang aman, presisi, serta sesuai dengan alur kerja masing-masing role pengguna.
