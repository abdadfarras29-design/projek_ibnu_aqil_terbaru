<?php
$KONEKSI = mysqli_connect("localhost", "root", "", "ibnu_aqil");

if (mysqli_connect_errno()) {
    echo "Koneksi gagal: " . mysqli_connect_error();
    exit();
}
?>