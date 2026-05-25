<?php

include '../config/db.php';

$order_id = $_GET['oid'] ?? null;

if ($order_id) {

    mysqli_query(
        $conn,
        "UPDATE orders
         SET status='Paid'
         WHERE id='$order_id'"
    );

    // Clear cart after successful payment
    session_start();

    if (isset($_SESSION['user_id'])) {

        $user_id = $_SESSION['user_id'];

        mysqli_query(
            $conn,
            "DELETE FROM cart WHERE user_id='$user_id'"
        );
    }
}

header("Location: ../order_success.php?order_id=" . $order_id);
exit();

?>