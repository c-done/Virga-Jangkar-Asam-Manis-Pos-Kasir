<?php
require __DIR__.'/config/koneksi.php';
if (empty($_SESSION['user'])) { header('Location: auth/login.php'); exit; }
header('Location: admin/dashboard.php'); exit;
