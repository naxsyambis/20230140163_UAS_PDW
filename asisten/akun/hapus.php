<?php
session_start();
require_once '../../config.php';

if ($_SESSION['role'] != 'asisten') { header("Location: ../../login.php"); exit(); }

$id = $_GET['id'];
$conn->query("DELETE FROM users WHERE id=$id");

header("Location: index.php");
exit();
?>
