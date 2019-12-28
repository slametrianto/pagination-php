<?php
session_start();

if (!isset($_SESSION["login"])) {
    header("location: login.php");
    exit;
}

require 'functions.php';

//pagination
//konfigurasi
$jumlahDataPerhalaman = 5;
// $result  = mysqli_query($conn, "SELECT * FROM mahasiswa");
// $jumlahData = mysqli_num_rows($result);
// var_dump($jumlahData);
//count untuk menghitung array didlm assosiative
$jumlahData = count(query("SELECT * FROM mahasiswa"));
// var_dump($jumlahdata);


//ceil bulatkan ketas, floor bulatkan kebawah
$jumlahHalaman = ceil($jumlahData / $jumlahDataPerhalaman);
// var_dump($jumlahHalaman);

// if (isset($_GET["page"])) {
//     $halamanAktif = $_GET["page"];
// } else {
//     $halamanAktif = 1;
// }

// var_dump($halamanAktif);
//ternary jika true diisi page dan jika false diisi 1
$halamanAktif = (isset($_GET['page'])) ? $_GET["page"] : 1;

// page = 2, awalanData = 2
// page = 3, awalanData = 4

$awalData = ($jumlahDataPerhalaman * $halamanAktif) - $jumlahDataPerhalaman;


//query untuk menampilkann data mahasiswa
$mahasiswa = query("SELECT * FROM mahasiswa LIMIT $awalData, $jumlahDataPerhalaman");

//jika nnti tombol cari diclick (cari)name button
if (isset($_POST["cari"])) {
    //membuat function cari
    $mahasiswa = cari($_POST["keyword"]);
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Halaman admin</title>
</head>

<body>

    <h1>Daftar Mahasiswa</h1>

    <a href="logout.php">Logout</a>
    <br>
    <br>
    <br>

    <a href="tambah.php">Tambah Data Mahasiswa</a>
    <br>
    <br>

    <form action="" method="post">
        <input type="text" name="keyword" size="30" autofocus placeholder="masukkan keyword pencarian" autocomplete="off">
        <button type="submit" name="cari">Cari!</button>
    </form>

    <br><br>

    <!-- navigasi -->
    <?php if ($halamanAktif > 1) : ?>
        <a href="?page=<?= $halamanAktif - 1 ?>">&laquo;</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $jumlahHalaman; $i++) : ?>
        <?php if ($i == $halamanAktif) : ?>
            <a href="?page=<?= $i; ?>" style="font-weight: bold; color: red;"><?= $i; ?></a>
        <?php else : ?>
            <a href="?page=<?= $i; ?>"><?= $i; ?></a>

        <?php endif; ?>
    <?php endfor; ?>


    <?php if ($halamanAktif < $jumlahHalaman) : ?>
        <a href="?page=<?= $halamanAktif + 1 ?>">&raquo;</a>
    <?php endif; ?>


    <br>
    <br>
    <br>

    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>No</th>
            <th>aksi</th>
            <th>gambar</th>
            <th>nrp</th>
            <th>nama</th>
            <th>email</th>
            <th>jurusan</th>
        </tr>

        <?php $i = 1; ?>
        <tr>
            <?php foreach ($mahasiswa as $row) : ?>


                <td><?php echo $i; ?></td>
                <td>
                    <a href="ubah.php?id=<?= $row["id"]; ?>">ubah</a> |
                    <a href="hapus.php?id=<?= $row["id"];  ?>" onclick="return confirm('yakin?');">hapus</a>
                </td>
                <td><img src="img/<?= $row["gambar"];  ?>" width="50"></td>
                <td><?php echo $row["nrp"]; ?></td>
                <td><?php echo $row["nama"]; ?></td>
                <td><?php echo $row["email"]; ?></td>
                <td><?= $row["jurusan"]; ?></td>
        </tr>
        <?php $i++; ?>

    <?php endforeach; ?>

    </table>
</body>

</html>