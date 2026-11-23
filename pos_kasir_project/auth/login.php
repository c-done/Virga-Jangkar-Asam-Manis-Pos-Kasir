<?php
require __DIR__.'/../config/koneksi.php';
if (!empty($_SESSION['user'])) { header('Location: ../admin/dashboard.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verify_csrf(); $email = trim($_POST['email'] ?? ''); $password = $_POST['password'] ?? '';
  $stmt = $pdo->prepare('SELECT id,nama,email,password,role FROM users WHERE email=? LIMIT 1'); $stmt->execute([$email]); $u = $stmt->fetch();
  if ($u && password_verify($password, $u['password'])) {
    session_regenerate_id(true); unset($u['password']); $_SESSION['user'] = $u; header('Location: ../admin/dashboard.php'); exit;
  } else $error = 'Email atau password tidak sesuai.';
}
?><!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Login | POS Kasir</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet"><link href="../assets/css/style.css" rel="stylesheet"></head><body class="login-page"><div class="login-card"><div class="brand justify-content-center mb-4"><span class="brand-icon"><i class="fa-solid fa-bag-shopping"></i></span><span>POS<span class="brand-blue">KASIR</span><small>SMART COMMERCE</small></span></div><h2>Selamat datang 👋</h2><p class="muted mb-4">Masuk untuk mengelola toko Anda.</p><?php if($error): ?><div class="alert alert-danger"><?=e($error)?></div><?php endif; ?><form method="post"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><label class="form-label">Email</label><input class="form-control mb-3" type="email" name="email" required autocomplete="username" placeholder="nama@email.com"><label class="form-label">Password</label><input class="form-control mb-4" type="password" name="password" required autocomplete="current-password" placeholder="Masukkan password"><button class="btn btn-primary w-100 py-2"><i class="fa-solid fa-arrow-right-to-bracket me-2"></i>Masuk</button></form><p class="muted small text-center mt-4 mb-0">Gunakan akun yang dibuat melalui setup_admin.php.</p></div></body></html>
