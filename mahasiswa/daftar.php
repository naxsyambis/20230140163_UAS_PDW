<?php
session_start();
require_once '../config.php';
if($_SESSION['role']!='mahasiswa'){ header("Location: ../login.php"); exit(); }

$id_praktikum = (int)$_GET['id'];
$id_mahasiswa = $_SESSION['user_id'];

// Cek apakah sudah terdaftar
$cek = $conn->query("SELECT * FROM peserta_praktikum WHERE id_praktikum=$id_praktikum AND id=$id_mahasiswa");
if($cek->num_rows==0){
    $conn->query("INSERT INTO peserta_praktikum (id_praktikum, id) VALUES ($id_praktikum, $id_mahasiswa)");
}
header("Location: praktikum_saya.php");
exit();
?>
