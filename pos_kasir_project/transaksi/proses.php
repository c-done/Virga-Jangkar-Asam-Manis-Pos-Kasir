<?php
require __DIR__.'/../config/koneksi.php';require_login();
if($_SERVER['REQUEST_METHOD']!=='POST'){header('Location: ../kasir/index.php');exit;}verify_csrf();
$items=$_POST['items']??[];$bayar=filter_var($_POST['bayar']??null,FILTER_VALIDATE_FLOAT);
if(!is_array($items)||!$items||$bayar===false||$bayar===null||$bayar<0){header('Location: ../kasir/index.php?error='.urlencode('Keranjang atau nominal pembayaran tidak valid.'));exit;}
$cart=[];foreach($items as $item){$id=filter_var($item['id']??null,FILTER_VALIDATE_INT);$qty=filter_var($item['qty']??null,FILTER_VALIDATE_INT);if(!$id||!$qty||$qty<1||$qty>10000){header('Location: ../kasir/index.php?error='.urlencode('Jumlah barang tidak valid.'));exit;}$cart[$id]=($cart[$id]??0)+$qty;}
try{
 $pdo->beginTransaction();$total=0;$profit=0;$lines=[];
 $find=$pdo->prepare('SELECT id,kode_produk,nama_produk,harga_modal,harga_jual,stok FROM produk WHERE id=? FOR UPDATE');
 foreach($cart as $id=>$qty){$find->execute([$id]);$p=$find->fetch();if(!$p)throw new Exception('Produk tidak ditemukan.');if((int)$p['stok']<$qty)throw new Exception('Stok '.$p['nama_produk'].' tidak mencukupi.');$sub=(float)$p['harga_jual']*$qty;$pr=((float)$p['harga_jual']-(float)$p['harga_modal'])*$qty;$total+=$sub;$profit+=$pr;$lines[]=['p'=>$p,'qty'=>$qty,'sub'=>$sub,'profit'=>$pr];}
 if($bayar<$total)throw new Exception('Uang pembayaran kurang dari total belanja.');
 $code='TRX'.date('YmdHis').strtoupper(bin2hex(random_bytes(2)));$change=$bayar-$total;
 $q=$pdo->prepare('INSERT INTO transaksi(kode_transaksi,user_id,total,bayar,kembalian,keuntungan) VALUES(?,?,?,?,?,?)');$q->execute([$code,$_SESSION['user']['id'],$total,$bayar,$change,$profit]);$tid=(int)$pdo->lastInsertId();
 $detail=$pdo->prepare('INSERT INTO detail_transaksi(transaksi_id,produk_id,nama_produk,harga,jumlah,subtotal,keuntungan) VALUES(?,?,?,?,?,?,?)');
 $stock=$pdo->prepare('UPDATE produk SET stok=stok-? WHERE id=? AND stok>=?');
 foreach($lines as $l){$p=$l['p'];$detail->execute([$tid,$p['id'],$p['nama_produk'],$p['harga_jual'],$l['qty'],$l['sub'],$l['profit']]);$stock->execute([$l['qty'],$p['id'],$l['qty']]);if($stock->rowCount()!==1)throw new Exception('Stok berubah saat transaksi. Silakan ulangi.');}
 $pdo->commit();header('Location: struk.php?id='.$tid);exit;
}catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();header('Location: ../kasir/index.php?error='.urlencode($e->getMessage()));exit;}
