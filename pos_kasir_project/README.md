# POS Kasir — PHP Native + MySQL

Aplikasi POS untuk tugas sekolah, berjalan di XAMPP. Tema dark navy/biru, dashboard, CRUD produk/kategori/pengguna, transaksi dengan penguncian stok, struk cetak, riwayat, laporan dan ekspor CSV.

## Instalasi di XAMPP
1. Salin folder `pos_kasir` ke `C:\xampp\htdocs\pos_kasir`.
2. Buka XAMPP Control Panel, jalankan **Apache** dan **MySQL**.
3. Buka `http://localhost/phpmyadmin`, pilih **Import**, lalu impor `database/db_pos_kasir.sql`.
4. Periksa `config/koneksi.php`. Default XAMPP: host `localhost`, user `root`, password kosong.
5. Buka `http://localhost/pos_kasir/setup_admin.php`. Buat akun admin dengan email dan password unik minimal 10 karakter. Buat akun kasir dari halaman Pengguna atau gunakan setup ini lagi untuk role kasir.
6. Setelah akun dibuat, **hapus `setup_admin.php`** agar orang lain tidak dapat membuat akun lewat halaman setup.
7. Login di `http://localhost/pos_kasir/auth/login.php`.

## Akun demo
SQL tidak menyimpan password default yang diketahui umum. Buat akun demo sendiri lewat `setup_admin.php`, misalnya nama `Admin Demo`, email `admin@example.test`, role Admin dan password unik minimal 10 karakter. Buat akun `Kasir Demo` dengan role Kasir. Jangan gunakan password contoh pada sistem produksi.

## Alur uji
- Admin: tambah kategori bila perlu, lalu tambah produk dengan stok awal.
- Buka Kasir, cari produk, tambah item, ubah kuantitas, masukkan pembayaran dan proses.
- Pastikan struk muncul, stok produk berkurang, transaksi tercatat di Riwayat, dan Laporan menghitung omzet/modal/keuntungan.
- Coba bayar kurang dan jumlah melebihi stok; transaksi harus ditolak.
- Uji akun Kasir: menu admin langsung dilindungi server-side, meski URL diketik manual.

## Catatan
- Halaman pengaturan toko: `admin/pengaturan.php` (tambahkan tautan menu bila ingin mudah diakses).
- Tema gelap/terang disimpan di browser.
- Chart.js, Bootstrap, Font Awesome, dan Google Fonts memakai CDN sehingga membutuhkan internet saat membuka halaman.
- Ini starter project edukasi. Sebelum dipakai di lingkungan nyata, tambahkan logging audit, backup, rate limiting login, kebijakan CSRF lebih luas, validasi bisnis tambahan, serta HTTPS.
