<?php
session_start();

if (!isset($_SESSION['status']) || $_SESSION['status'] != 'login') {
    header("Location: ../login.php");
    exit();
}

include "../config/koneksi.php";

$query = mysqli_query($koneksi, "SELECT * FROM tanaman ORDER BY kode_tanaman ASC");

$totalData = mysqli_num_rows($query);
$totalStok = 0;
$totalNilai = 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Data Tanaman Hias</title>

<style>

body{
    font-family: Arial, sans-serif;
    margin:20px;
}

h2{
    text-align:center;
    margin-bottom:5px;
}

.info{
    text-align:center;
    margin-bottom:20px;
}

button{
    background:#2e7d32;
    color:#fff;
    border:none;
    padding:10px 18px;
    border-radius:5px;
    cursor:pointer;
    margin-bottom:20px;
}

table{
    width:100%;
    border-collapse:collapse;
}

th,td{
    border:1px solid #000;
    padding:8px;
    font-size:12px;
    vertical-align:middle;
}

th{
    background:#2e7d32;
    color:#fff;
}

img{
    width:70px;
    height:70px;
    object-fit:cover;
    border-radius:5px;
}

.center{
    text-align:center;
}

.right{
    text-align:right;
}

@media print{

button{
    display:none;
}

body{
    margin:10px;
}

}

</style>

</head>
<body>

<button onclick="window.print()">🖨 Cetak Laporan</button>

<h2>LAPORAN DATA TANAMAN HIAS</h2>

<div class="info">
    Dicetak :
    <?= date('d-m-Y H:i'); ?>
    <br>
    Oleh :
    <?= $_SESSION['nama']; ?>
    <br>
    Total Data :
    <?= $totalData; ?>
</div>

<table>

<tr>
    <th>No</th>
    <th>Foto</th>
    <th>Kode</th>
    <th>Nama Tanaman</th>
    <th>Kategori</th>
    <th>Harga</th>
    <th>Stok</th>
    <th>Tanggal</th>
    <th>Deskripsi</th>
</tr>

<?php
$no = 1;

while($row = mysqli_fetch_assoc($query)){

$totalStok += $row['stok'];
$totalNilai += ($row['harga'] * $row['stok']);
?>

<tr>

<td class="center">
<?= $no++; ?>
</td>

<td class="center">

<?php
if(!empty($row['gambar']) && file_exists("../assets/upload/".$row['gambar'])){
?>

<img src="../assets/upload/<?= $row['gambar']; ?>" alt="Tanaman">

<?php
}else{
echo "Tidak ada";
}
?>

</td>

<td><?= $row['kode_tanaman']; ?></td>

<td><?= $row['nama_tanaman']; ?></td>

<td class="center"><?= $row['kategori']; ?></td>

<td class="right">
Rp <?= number_format($row['harga'],0,',','.'); ?>
</td>

<td class="center">
<?= $row['stok']; ?>
</td>

<td class="center">
<?= date('d/m/Y',strtotime($row['tanggal_input'])); ?>
</td>

<td>
<?= $row['deskripsi']; ?>
</td>

</tr>

<?php } ?>

<tr style="background:#dcedc8;font-weight:bold;">

<td colspan="5" class="right">
TOTAL
</td>

<td class="right">
Rp <?= number_format($totalNilai,0,',','.'); ?>
</td>

<td class="center">
<?= $totalStok; ?>
</td>

<td colspan="2"></td>

</tr>

</table>

<br>

<p style="text-align:center;color:#777;">
Dokumen ini dibuat otomatis oleh Sistem Data Tanaman Hias.
</p>

<script>
window.onload = function(){
    window.print();
}
</script>

</body>
</html>