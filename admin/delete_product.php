<?php
include '../config/db.php';

$id = (int) $_GET['id'];

mysqli_query($conn, "DELETE FROM products WHERE id = $id");

header("Location: products.php");