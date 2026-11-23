<?php
require __DIR__.'/../config/koneksi.php'; require_login();
$totalProduk=(int)$pdo->query('SELECT COUNT(*) FROM produk')->fetchColumn();
$stok=(int)$pdo->query('SELECT COALESCE(SUM(stok),0) FROM produk')->fetchColumn();
$hari=$pdo->query("SELECT COUNT(*) jumlah,COALESCE(SUM(total),0) omzet FROM transaksi WHERE DATE(tanggal)=CURDATE()")->fetch();
$recent=$pdo->query('SELECT t.kode_transaksi,t.tanggal,t.total,u.nama kasir FROM transaksi t LEFT JOIN users u ON u.id=t.user_id ORDER BY t.id DESC LIMIT 7')->fetchAll();
$laris=$pdo->query('SELECT dt.nama_produk,SUM(dt.jumlah) jumlah FROM detail_transaksi dt GROUP BY dt.nama_produk ORDER BY jumlah DESC LIMIT 5')->fetchAll();
$chart=$pdo->query("SELECT DATE(tanggal) hari,SUM(total) total FROM transaksi WHERE tanggal>=DATE_SUB(CURDATE(),INTERVAL 6 DAY) GROUP BY DATE(tanggal) ORDER BY hari")->fetchAll();
$labels=[];$values=[];for($i=6;$i>=0;$i--){$d=date('Y-m-d',strtotime("-$i day"));$labels[]=date('d/m',strtotime($d));$values[]=0;foreach($chart as $c)if($c['hari']===$d)$values[count($values)-1]=(float)$c['total'];}
page_start('Dashboard');
?>
<div class="welcome-panel mb-4"><div><span class="eyebrow">RINGKASAN TOKO</span><h2>Halo, <?=e($_SESSION['user']['nama'])?>!</h2><p>Berikut ringkasan aktivitas toko Anda hari ini.</p></div><div class="welcome-icon"><i class="fa-solid fa-chart-line"></i></div></div>
<div class="row g-3 mb-4">
<div class="col-6 col-xl-3"><div class="stat-card"><div class="stat-icon blue"><i class="fa-solid fa-box"></i></div><span>Total Produk</span><strong><?=number_format($totalProduk)?></strong><small>Produk terdaftar</small></div></div>
<div class="col-6 col-xl-3"><div class="stat-card"><div class="stat-icon purple"><i class="fa-solid fa-receipt"></i></div><span>Transaksi Hari Ini</span><strong><?=number_format((int)$hari['jumlah'])?></strong><small>Transaksi berhasil</small></div></div>
<div class="col-6 col-xl-3"><div class="stat-card"><div class="stat-icon green"><i class="fa-solid fa-wallet"></i></div><span>Omzet Hari Ini</span><strong class="money"><?=rupiah($hari['omzet'])?></strong><small>Total penjualan</small></div></div>
<div class="col-6 col-xl-3"><div class="stat-card"><div class="stat-icon orange"><i class="fa-solid fa-cubes-stacked"></i></div><span>Total Stok</span><strong><?=number_format($stok)?></strong><small>Unit barang</small></div></div>
</div>
<div class="row g-3"><div class="col-xl-8"><div class="panel-card"><div class="panel-heading"><div><h3>Grafik Penjualan</h3><p>7 hari terakhir</p></div></div><canvas id="salesChart" height="115"></canvas></div></div><div class="col-xl-4"><div class="panel-card"><div class="panel-heading"><div><h3>Produk Terlaris</h3><p>Berdasarkan jumlah terjual</p></div></div><?php if(!$laris): ?><p class="muted">Belum ada data penjualan.</p><?php else: foreach($laris as $i=>$p): ?><div class="top-product"><span class="rank"><?=($i+1)?></span><div><strong><?=e($p['nama_produk'])?></strong><small><?=number_format($p['jumlah'])?> unit terjual</small></div></div><?php endforeach; endif; ?></div></div>
<div class="col-12"><div class="panel-card"><div class="panel-heading"><div><h3>Transaksi Terbaru</h3><p>Aktivitas transaksi terakhir</p></div><a class="btn btn-sm btn-outline-primary" href="../transaksi/riwayat.php">Lihat semua</a></div><div class="table-responsive"><table class="table modern-table"><thead><tr><th>Kode Transaksi</th><th>Tanggal</th><th>Kasir</th><th class="text-end">Total</th></tr></thead><tbody><?php if(!$recent): ?><tr><td colspan="4" class="text-center muted py-4">Belum ada transaksi.</td></tr><?php else: foreach($recent as $r): ?><tr><td><strong><?=e($r['kode_transaksi'])?></strong></td><td><?=e($r['tanggal'])?></td><td><?=e($r['kasir']??'-')?></td><td class="text-end fw-bold"><?=rupiah($r['total'])?></td></tr><?php endforeach; endif; ?></tbody></table></div></div></div></div>

    <script>window.salesLabels=<?=json_encode($labels)?>;window.salesValues=<?=json_encode($values)?>;</script>
<?php page_end(); ?>
