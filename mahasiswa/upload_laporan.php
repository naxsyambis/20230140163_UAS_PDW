<?php
session_start();
require_once '../config.php';
if($_SESSION['role']!='mahasiswa'){ header("Location: ../login.php"); exit(); }

$id_mahasiswa = $_SESSION['user_id'];
$id_modul = (int)$_POST['id_modul'];

// Upload file
$filename = uniqid().'_'.basename($_FILES['file_laporan']['name']);
move_uploaded_file($_FILES['file_laporan']['tmp_name'], "../uploads/".$filename);

// Simpan ke DB
$conn->query("INSERT INTO laporan_mahasiswa (id_modul, id, File_laporan) VALUES ($id_modul, $id_mahasiswa, '$filename')");

header("Location: detail_praktikum.php?id=".$conn->query("SELECT id_praktikum FROM modul WHERE id_modul=$id_modul")->fetch_assoc()['id_praktikum']);
exit();
?>
