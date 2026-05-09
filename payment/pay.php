<?php
// ==============================
// SHOW ERRORS (DEV ONLY)
// ==============================
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ==============================
// DATABASE CONFIGURATION
// ==============================
$host = "localhost";
$user = "dainikni_user_libmnage";
$pass = "(?&exw0(lat_";
$db   = "dainikni_rman_lib_manage";

// ==============================
// CREATE CONNECTION
// ==============================
$conn = new mysqli($host, $user, $pass, $db);

$message = '';
$order   = null;
$details = [];

// ==============================
// PAYU CONFIG
// ==============================
$payuKey    = "Bt9ewB";
$payuSalt   = "7IFy0vguFZdI5of0eOIBZhGgWa2wmU5e";
// $payuBaseUrl = "https://secure.payu.in/_payment"; // Production
$payuBaseUrl = "https://test.payu.in/_payment"; // Test

$txnid = "TXN" . time(); // Unique txn id

// ==============================
// DB CONNECTION CHECK
// ==============================
if ($conn->connect_error) {
    $message = '<div class="alert alert-danger">Database Error: '.$conn->connect_error.'</div>';
}

// ==============================
// GET ORDER ID
// ==============================
$orderId = trim($_GET['order_id'] ?? '');

// ==============================
// MAIN LOGIC
// ==============================
if (!$message) {

    if (empty($orderId)) {
        $message = '<div class="alert alert-danger">Invalid Order ID</div>';
    } else {

        // FETCH ORDER + USER
        $stmt = $conn->prepare("
            SELECT 
                orders.*, 
                users.name AS user_name,
                users.email AS user_email,
                users.mobile AS user_mobile
            FROM orders
            LEFT JOIN users ON users.id = orders.user_id
            WHERE orders.order_number = ?
        ");

        if (!$stmt) {
            $message = '<div class="alert alert-danger">SQL Error: '.$conn->error.'</div>';
        } else {

            $stmt->bind_param("s", $orderId);

            if ($stmt->execute()) {

                $result = $stmt->get_result();

                if ($result && $result->num_rows > 0) {

                    $order = $result->fetch_assoc();
                    $message = '<div class="alert alert-success">Order Found Successfully</div>';

                    // ==============================
                    // FETCH ORDER DETAILS
                    // ==============================
                    $detailStmt = $conn->prepare("
                        SELECT 
                            order_id,
                            type,
                            type_id,
                            item_details,
                            amount
                        FROM order_details
                        WHERE order_id = ?
                    ");

                    if ($detailStmt) {

                        $detailStmt->bind_param("s", $order['id']);

                        if ($detailStmt->execute()) {

                            $detailResult = $detailStmt->get_result();

                            while ($row = $detailResult->fetch_assoc()) {

                                $jsonData = json_decode($row['item_details'], true);

                                $name = 'N/A';

                                if ($row['type'] == 'BOOK') {
                                    $name = $jsonData['book_name'] ?? 'N/A';
                                } elseif ($row['type'] == 'PLAN') {
                                    $name = $jsonData['plan_name'] ?? 'N/A';
                                }

                                $details[] = [
                                    'type'   => $row['type'],
                                    'id'     => $row['type_id'],
                                    'name'   => $name,
                                    'amount' => $row['amount']
                                ];
                            }
                        }

                        $detailStmt->close();
                    }

                    // ==============================
                    // PAYU DATA PREPARE
                    // ==============================
                    $amount      = $order['amount'];
                    $productinfo = "Order Payment";
                    $firstname   = $order['user_name'] ?? 'Customer';
                    $email       = $order['user_email'] ?? 'test@test.com';
                    $phone       = $order['user_mobile'] ?? '9999999999';

                    $surl = "https://dainiknirman.com/payment/success.php";
                    $furl = "https://dainiknirman.com/payment/failure.php";

                    // HASH GENERATION
                    $udf1 = $order['order_number'];
                    $udf2 = "";
                    $udf3 = "";
                    $udf4 = "";
                    $udf5 = "";

                    $hashString = $payuKey . "|" . $txnid . "|" . $amount . "|" . $productinfo . "|" . $firstname . "|" . $email . "|" .
                                $udf1 . "|" . $udf2 . "|" . $udf3 . "|" . $udf4 . "|" . $udf5 . "||||||" . $payuSalt;

                    $hash = strtolower(hash('sha512', $hashString));

                } else {
                    $message = '<div class="alert alert-warning">Order Not Found</div>';
                }

            } else {
                $message = '<div class="alert alert-danger">Execute Error: '.$stmt->error.'</div>';
            }

            $stmt->close();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Pay Now</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container mt-5">

    <h3>Order Payment</h3>

    <?= $message; ?>

    <?php if (!empty($order)) { ?>

        <div class="card mt-3 p-3 shadow">
            <p><strong>Order ID:</strong> <?= $order['order_number'] ?></p>
            <p><strong>Name:</strong> <?= $firstname ?></p>
            <p><strong>Amount:</strong> ₹<?= $amount ?></p>
        </div>

        <!-- PAYU FORM -->
        <form id="payuForm" method="post" action="<?= $payuBaseUrl ?>">

            <input type="hidden" name="key" value="<?= $payuKey ?>" />
            <input type="hidden" name="txnid" value="<?= $txnid ?>" />
            <input type="hidden" name="amount" value="<?= $amount ?>" />
            <input type="hidden" name="productinfo" value="<?= $productinfo ?>" />
            <input type="hidden" name="firstname" value="<?= $firstname ?>" />
            <input type="hidden" name="email" value="<?= $email ?>" />
            <input type="hidden" name="phone" value="<?= $phone ?>" />
            <input type="hidden" name="surl" value="<?= $surl ?>" />
            <input type="hidden" name="furl" value="<?= $furl ?>" />
            <input type="hidden" name="hash" value="<?= $hash ?>" />
            <input type="hidden" name="udf1" value="<?= $order['order_number'] ?>" />

        </form>

        <div class="alert alert-info mt-3">
            Redirecting to PayU in 5 seconds...
        </div>

    <?php } ?>

</div>

<!-- AUTO REDIRECT -->
<script>
    setTimeout(function () {
        var form = document.getElementById("payuForm");
        if (form) form.submit();
    }, 5000);
</script>

</body>
</html>