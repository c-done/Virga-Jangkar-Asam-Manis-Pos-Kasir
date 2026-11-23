<?php
// Jalankan sekali dari browser: http://localhost/pos_kasir/setup_admin.php lalu HAPUS file ini setelah akun dibuat.
require __DIR__.'/config/koneksi.php';
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verify_csrf();
  $nama=trim($_POST['nama']??''); $email=trim($_POST['email']??''); $password=$_POST['password']??''; $role=$_POST['role']??'admin';
  if ($nama==='' || !filter_var($email,FILTER_VALIDATE_EMAIL) || strlen($password)<10 || !in_array($role,['admin','kasir'],true)) $msg='Isi semua data dengan benar. Password minimal 10 karakter.';
  else {
    try { $q=$pdo->prepare('INSERT INTO users(nama,email,password,role) VALUES(?,?,?,?)'); $q->execute([$nama,$email,password_hash($password,PASSWORD_DEFAULT),$role]); $msg='Akun berhasil dibuat. Demi keamanan, hapus setup_admin.php sekarang.'; }
    catch(PDOException $e) { $msg='Akun gagal dibuat. Email mungkin sudah digunakan.'; }
  }
}
?><!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Setup akun</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link href="assets/css/style.css" rel="stylesheet"></head><body class="login-page"><div class="login-card"><h2>Buat akun awal</h2><p class="muted">Buat admin pertama atau akun kasir. Password minimal 10 karakter.</p><?php if($msg): ?><div class="alert alert-info"><?=e($msg)?></div><?php endif; ?><form method="post"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><label class="form-label">Nama</label><input class="form-control mb-3" name="nama" required><label class="form-label">Email</label><input class="form-control mb-3" type="email" name="email" required><label class="form-label">Password</label><input class="form-control mb-3" type="password" name="password" minlength="10" required><label class="form-label">Role</label><select class="form-select mb-4" name="role"><option value="admin">Admin</option><option value="kasir">Kasir</option></select><button class="btn btn-primary w-100">Buat akun</button></form><a class="d-block mt-3" href="auth/login.php">Ke halaman login</a></div></body></html>
