<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ==============================
// DB CONNECTION
// ==============================
$conn = new mysqli("localhost", "dainikni_user_libmnage", "(?&exw0(lat_", "dainikni_rman_lib_manage");

if ($conn->connect_error) {
    die("DB Connection Failed: " . $conn->connect_error);
}

// ==============================
// GET DATA FROM PAYU
// ==============================
$status    = $_POST["status"] ?? '';
$txnid     = $_POST["txnid"] ?? '';
$orderId   = $_POST["udf1"] ?? '';
$errorMsg  = $_POST["error_Message"] ?? $_POST["error"] ?? 'Payment declined';

// ==============================
// UPDATE ORDER AS FAILED
// ==============================
if (!empty($orderId)) {
    $stmt = $conn->prepare("
        UPDATE orders 
        SET transaction_status = 'FAILED', 
            transaction_id = ? 
        WHERE order_number = ?
    ");
    $stmt->bind_param("ss", $txnid, $orderId);
    $stmt->execute();
    $stmt->close();
}

// ==============================
// FETCH ORDER DATA
// ==============================
$orderData = null;

if (!empty($orderId)) {
    $stmt = $conn->prepare("SELECT * FROM orders WHERE order_number = ?");
    $stmt->bind_param("s", $orderId);
    $stmt->execute();

    $result = $stmt->get_result();
    $orderData = $result->fetch_assoc();

    $stmt->close();
}

// ==============================
// FETCH TYPE + TYPE_ID
// ==============================
$type = 'BOOK'; 
$type_id = '';

if ($orderData) {

    $stmt = $conn->prepare("
        SELECT type, type_id 
        FROM order_details 
        WHERE order_id = ? 
        LIMIT 1
    ");

    $stmt->bind_param("i", $orderData['id']);
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    if ($row) {
        $type = $row['type'] ?? 'BOOK';
        $type_id = $row['type_id'];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment Failed</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container mt-5 text-center">

    <h3 class="text-danger">Payment Failed ❌</h3>

    <div class="card mt-4 p-4 shadow text-start">

        <p><strong>Transaction ID:</strong> <?= htmlspecialchars($txnid) ?></p>
        <p><strong>Order Number:</strong> <?= htmlspecialchars($orderId) ?></p>

        <?php if ($orderData): ?>
            <p><strong>Amount:</strong> ₹<?= htmlspecialchars($orderData['amount']) ?></p>
            <p><strong>Status:</strong> <?= htmlspecialchars($orderData['transaction_status']) ?></p>
        <?php endif; ?>

        <p><strong>Reason:</strong> <?= htmlspecialchars($errorMsg) ?></p>

        <p class="mt-3">Please try again.</p>

    </div>

    <p class="text-muted mt-3">Redirecting to app in 5 seconds...</p>

    <a href="index.php" class="btn btn-primary mt-3">Try Again</a>

</div>

<!-- ==============================
     REDIRECT SCRIPT
============================== -->
<script>
    setTimeout(function () {

        var type = "<?= $type ?>";
        var reason = encodeURIComponent("<?= $errorMsg ?>");

        window.location.href =
            "dainiknirman://payment/failed?reason=" + reason + "&type=" + type;
            // Close the browser after a short delay
            setTimeout(() => {
            window.close();
            }, 500);
    }, 5000);
</script>

</body>
</html>