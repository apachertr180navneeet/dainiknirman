<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ==============================
// DB CONFIG
// ==============================
$host = "localhost";
$user = "dainikni_user_libmnage";
$pass = "(?&exw0(lat_";
$db   = "dainikni_rman_lib_manage";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("DB Connection Failed: " . $conn->connect_error);
}

// ==============================
// PAYU CONFIG
// ==============================
$payuKey  = "Bt9ewB";
$payuSalt = "7IFy0vguFZdI5of0eOIBZhGgWa2wmU5e";
// $payuBaseUrl = "https://secure.payu.in/_payment"; // LIVE
$payuBaseUrl = "https://test.payu.in/_payment"; // TEST

// ==============================
// GET ORDER ID
// ==============================
$orderId = $_GET['order_id'] ?? '';

if (!ctype_digit($orderId)) {
    die("Invalid Order ID");
}

// ==============================
// FETCH ORDER
// ==============================
$stmt = $conn->prepare("
    SELECT 
        pu.*, 
        u.name AS user_name,
        u.mobile AS user_mobile,
        u.email AS user_email
    FROM plan_users pu
    LEFT JOIN users u ON u.id = pu.user_id
    WHERE pu.id = ?
");

if (!$stmt) {
    die("SQL Error: " . $conn->error);
}

$stmt->bind_param("i", $orderId);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows == 0) {
    die("Order Not Found");
}

$order = $result->fetch_assoc();

// ==============================
// PREPARE PAYMENT DATA
// ==============================
$txnid = "TXN" . time() . rand(100,999);

// ✅ FIXED AMOUNT FORMAT
$amount = isset($order['subscription_amount']) ? (float)$order['subscription_amount'] : 1;
if ($amount < 1) {
    $amount = 1.00;
}
$amount = number_format($amount, 2, '.', ''); // MUST BE 1.00 format

$firstname   = trim($order['user_name'] ?? 'Guest');
$email       = trim($order['user_email'] ?? 'test@gmail.com');
$phone       = trim($order['user_mobile'] ?? '9999999999');
$productinfo = "Plan Purchase - " . trim($order['order_number']);

// ✅ UDF1 (IMPORTANT)
$udf1 = trim($order['order_number']);

$surl = "https://dainiknirman.com/payment/subsuccess.php";
$furl = "https://dainiknirman.com/payment/subfailure.php";

// ==============================
// CORRECT HASH (FINAL FIX)
// ==============================
$hashString = $payuKey . "|" . $txnid . "|" . $amount . "|" . $productinfo . "|" . $firstname . "|" . $email . "|" . $udf1 . "||||||||||" . $payuSalt;

$hash = strtolower(hash("sha512", $hashString));

// DEBUG (optional)
// echo "<pre>".$hashString."</pre>"; exit;

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pay Now</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">

    <h3>Order Payment</h3>

    <div class="card p-3 shadow">
        <p><strong>Order ID:</strong> <?= htmlspecialchars($order['order_number']) ?></p>
        <p><strong>Name:</strong> <?= htmlspecialchars($firstname) ?></p>
        <p><strong>Amount:</strong> ₹<?= htmlspecialchars($amount) ?></p>
    </div>

    <!-- PAYU FORM -->
    <form id="payuForm" method="post" action="<?= $payuBaseUrl ?>">

        <input type="hidden" name="key" value="<?= $payuKey ?>">
        <input type="hidden" name="txnid" value="<?= $txnid ?>">
        <input type="hidden" name="amount" value="<?= $amount ?>">
        <input type="hidden" name="productinfo" value="<?= $productinfo ?>">
        <input type="hidden" name="firstname" value="<?= $firstname ?>">
        <input type="hidden" name="email" value="<?= $email ?>">
        <input type="hidden" name="phone" value="<?= $phone ?>">
        <input type="hidden" name="surl" value="<?= $surl ?>">
        <input type="hidden" name="furl" value="<?= $furl ?>">
        <input type="hidden" name="hash" value="<?= $hash ?>">
        <input type="hidden" name="udf1" value="<?= $udf1 ?>">

        <!-- <button type="submit" class="btn btn-primary mt-3">Pay Now</button> -->
    </form>

    <div class="alert alert-info mt-3">
        Redirecting to PayU in 3 seconds...
    </div>

</div>

<script>
    setTimeout(function () {
        document.getElementById("payuForm").submit();
    }, 3000);
</script>

</body>
</html>