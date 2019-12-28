<?php

//koneksi database
$conn = mysqli_connect("localhost", "root", "", "phpdasar");


//functions query

function query($query)
{
    global $conn;

    $result = mysqli_query($conn, $query);

    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {

        $rows[] = $row;
    }

    return $rows;
}


function tambah($data)
{

    global $conn;

    //ambil data dari tiap elemen dalam form
    $nrp = htmlspecialchars($data["nrp"]);
    $nama = htmlspecialchars($data["nama"]);
    $email = htmlspecialchars($data["email"]);
    $jurusan = htmlspecialchars($data["jurusan"]);

    //upload gambar
    $gambar = upload();
    if (!$gambar) {
        return false;
    }

    //query insert data
    $query = "INSERT INTO mahasiswa
     VALUES ('', '$nrp', '$nama', '$email', '$jurusan', '$gambar')";




    mysqli_query($conn, $query);

    return mysqli_affected_rows($conn);
}


function upload()
{
    //gambar dpt dr name ditambah.php
    $namaFile = $_FILES['gambar']['name'];
    $ukuranFile = $_FILES['gambar']['size'];
    $error = $_FILES['gambar']['error'];
    $tmpName = $_FILES['gambar']['tmp_name'];

    //cek apakah tidak ada gambar yang diupload

    if ($error === 4) {
        echo "<script>
       alert('pilih gambar terlebih dahulu');
       </script>";
        return false;
    }

    // cek apakah yang diupload adalah gambar
    $ekstensiGambarValid = ['jpg', 'jpeg', 'png'];
    //perubahan explode sandika.jpg = ['sandhika', 'jpg'];
    $ekstensiGambar = explode('.', $namaFile);
    //sandika.galih.jpg ngambil paling akhir
    //ambil semua huruf kecil strtolower
    $ekstensiGambar = strtolower(end($ekstensiGambar));
    //ngecek string ada tdk disebuah array menghasilkan nilai true 
    if (!in_array($ekstensiGambar, $ekstensiGambarValid)) {

        echo "<script>alert('yang anda upload bukan gambar');</script>";
        return false;
    }

    //cek jika ukuran terlalu besar
    if ($ukuranFile > 1000000) {
        echo "<script>alert('ukuran gambar terlalu besar');</script>";
        return false;
    }

    //lolos pengecekan, gambar siap diupload move_uploaded_file buat mindahin file
    //generate nama gambar baru

    $namaFileBaru = uniqid();
    // var_dump($namaFileBaru);
    // die;0
    $namaFileBaru .= '.';
    $namaFileBaru .= $ekstensiGambar;
    // var_dump($namaFileBaru);
    // die;
    //untuk memindahkan gambar yg diupload
    move_uploaded_file($tmpName, 'img/' . $namaFileBaru);
    return $namaFileBaru;
}


function hapus($id)
{

    global $conn;
    mysqli_query($conn, "DELETE FROM mahasiswa WHERE id = $id");
    return mysqli_affected_rows($conn);
}


function ubah($data)
{
    global $conn;

    //ambil data dari tiap elemen dalam form
    $id = $data["id"];

    $nrp = htmlspecialchars($data["nrp"]);
    $nama = htmlspecialchars($data["nama"]);
    $email = htmlspecialchars($data["email"]);
    $jurusan = htmlspecialchars($data["jurusan"]);
    $gambarLama = htmlspecialchars($data["gambarLama"]);

    //cek apakah user pilih gambar baru atau tidak
    if ($_FILES["gambar"]["error"] === 4) {
        $gambar = $gambarLama;
    } else {
        $gambar = upload();
    }



    //query update data
    $query = "UPDATE mahasiswa SET 
    nrp = '$nrp', 
    nama = '$nama',
    email = '$email',
    jurusan ='$jurusan',
    gambar = '$gambar'
    WHERE id = $id
    ";



    mysqli_query($conn, $query);

    //kembalikan ketika sudah trupdate databaseny
    return mysqli_affected_rows($conn);
}

function cari($keyword)
{
    $query = "SELECT * FROM mahasiswa  WHERE 
    nama LIKE '%$keyword%' OR
    email LIKE '%$keyword%' OR
    jurusan LIKE '%$keyword%' 
    ";
    return query($query);
}

function registrasi($data)
{
    global $conn;

    $username = strtolower(stripslashes($data["username"]));
    $password = mysqli_real_escape_string($conn, $data["password"]);
    $password2 = mysqli_real_escape_string($conn, $data["password2"]);


    //cek username sudah ada atau belum
    $result = mysqli_query($conn, "SELECT username FROM user WHERE username='$username'");

    if (mysqli_fetch_assoc($result)) {
        echo "<script>
       alert('username sudah terdaftar')
        </script>";
        return false;
    }

    //cekk konfirmasi password
    if ($password !== $password2) {

        echo "<script>
        alert('konfirmasi password tidak sesuai');
        </script>";
        return false;
    }

    //enkripsi password
    $password = password_hash($password, PASSWORD_DEFAULT);
    // var_dump($password);
    // die;

    //tambahkan userbaru kedatabase
    mysqli_query($conn, "INSERT INTO user VALUES('','$username','$password')");
    return mysqli_affected_rows($conn);
}
