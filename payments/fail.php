<?php

include '../config/db.php';

$order_id = $_GET['oid'] ?? null;

if ($order_id) {

    mysqli_query(
        $conn,
        "UPDATE orders
         SET status='Failed'
         WHERE id='$order_id'"
    );
}

header("Location: ../checkout.php");
exit();

?>