<?php

include '../config/db.php';

$order_id = $_GET['order_id'];

$result = mysqli_query(
    $conn,
    "SELECT * FROM orders WHERE id='$order_id'"
);

$order = mysqli_fetch_assoc($result);

$total = $order['total_amount'];

?>

<!DOCTYPE html>
<html>
<head>
    <title>Pay with eSewa</title>
</head>

<body>

<h2>Redirecting to eSewa...</h2>

<form id="esewaForm"
      action="https://rc-epay.esewa.com.np/api/epay/main/v2/form"
      method="POST">

    <input type="hidden" name="amount" value="<?= $total ?>">

    <input type="hidden" name="tax_amount" value="0">

    <input type="hidden" name="total_amount" value="<?= $total ?>">

    <input type="hidden" name="transaction_uuid"
           value="<?= $order_id ?>">

    <input type="hidden" name="product_code"
           value="EPAYTEST">

    <input type="hidden" name="product_service_charge"
           value="0">

    <input type="hidden" name="product_delivery_charge"
           value="0">

    <input type="hidden"
           name="success_url"
           value="http://localhost/giftshop/payments/success.php">

    <input type="hidden"
           name="failure_url"
           value="http://localhost/giftshop/payments/fail.php">

    <input type="hidden"
           name="signed_field_names"
           value="total_amount,transaction_uuid,product_code">

    <input type="hidden"
           name="signature"
           value="">

    <button type="submit">
        Pay with eSewa
    </button>

</form>

</body>
</html>