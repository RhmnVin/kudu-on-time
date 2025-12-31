<?php
include '../config/database.php';
$conn = Database::getConnection();

$id = $_GET['id'];
$conn->query("DELETE FROM courses WHERE id=$id");

header("Location: index.php");
