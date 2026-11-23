<?php
declare(strict_types=1);
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$host = 'localhost'; $db = 'db_pos_kasir'; $user = 'root'; $pass = '';
try {
  $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false
  ]);
} catch (PDOException $e) { http_response_code(500); exit('Koneksi database gagal. Pastikan MySQL aktif dan database db_pos_kasir sudah diimpor.'); }
function e($value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function rupiah($value): string { return 'Rp ' . number_format((float)$value, 0, ',', '.'); }
function require_login(): void { if (empty($_SESSION['user'])) { header('Location: ../auth/login.php'); exit; } }
function require_admin(): void { require_login(); if (($_SESSION['user']['role'] ?? '') !== 'admin') { http_response_code(403); exit('Akses ditolak. Halaman ini khusus admin.'); } }
function csrf_token(): string { if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32)); return $_SESSION['csrf']; }
function verify_csrf(): void { if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) { http_response_code(419); exit('Token keamanan tidak valid. Muat ulang halaman.'); } }
function page_start(string $title): void {
  $name = $_SESSION['user']['nama'] ?? 'Pengunjung'; $role = $_SESSION['user']['role'] ?? '';
  echo '<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>'.e($title).' | POS Kasir</title>';
  echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet"><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"><link href="../assets/css/style.css" rel="stylesheet"></head><body><div class="app-shell"><aside class="sidebar"><a class="brand" href="../index.php"><span class="brand-icon"><i class="fa-solid fa-bag-shopping"></i></span><span>POS<span class="brand-blue">KASIR</span><small>SMART COMMERCE</small></span></a><div class="nav-caption">MENU UTAMA</div>';
  echo '<a class="nav-link-custom" href="../admin/dashboard.php"><i class="fa-solid fa-chart-pie"></i> Dashboard</a>';
  if ($role === 'admin') { echo '<a class="nav-link-custom" href="../admin/produk.php"><i class="fa-solid fa-box"></i> Produk</a><a class="nav-link-custom" href="../admin/kategori.php"><i class="fa-solid fa-tags"></i> Kategori</a><a class="nav-link-custom" href="../admin/pengguna.php"><i class="fa-solid fa-users"></i> Pengguna</a><a class="nav-link-custom" href="../laporan/index.php"><i class="fa-solid fa-chart-line"></i> Laporan</a>'; }
  echo '<a class="nav-link-custom" href="../kasir/index.php"><i class="fa-solid fa-cash-register"></i> Kasir</a><a class="nav-link-custom" href="../transaksi/riwayat.php"><i class="fa-solid fa-clock-rotate-left"></i> Riwayat Transaksi</a><div class="sidebar-bottom"><div class="user-mini"><div class="avatar">'.e(strtoupper(substr($name,0,1))).'</div><div><strong>'.e($name).'</strong><small>'.e(strtoupper($role)).'</small></div></div><a class="nav-link-custom logout-link" href="../auth/logout.php"><i class="fa-solid fa-right-from-bracket"></i> Keluar</a></div></aside><main class="main-content"><header class="topbar"><div><span class="eyebrow">WORKSPACE / POS</span><h1>'.e($title).'</h1></div><button class="btn btn-outline-light btn-sm" id="themeToggle" type="button"><i class="fa-solid fa-circle-half-stroke"></i> Tema</button></header><section class="page-body">';
}
function page_end(): void { echo '</section></main></div><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script><script src="https://cdn.jsdelivr.net/npm/chart.js"></script><script src="../assets/js/script.js"></script></body></html>'; }
