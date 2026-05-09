<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ==============================
// DB CONFIG
// ==============================
$conn = new mysqli("localhost", "dainikni_user_libmnage", "(?&exw0(lat_", "dainikni_rman_lib_manage");

if ($conn->connect_error) {
    die("DB Connection Failed: " . $conn->connect_error);
}

// ==============================
// PAYU CONFIG
// ==============================
$payuSalt = "7IFy0vguFZdI5of0eOIBZhGgWa2wmU5e";

// ==============================
// GET POST DATA FROM PAYU
// ==============================
$status        = $_POST["status"] ?? '';
$firstname     = $_POST["firstname"] ?? '';
$amount        = $_POST["amount"] ?? '';
$txnid         = $_POST["txnid"] ?? '';
$posted_hash   = $_POST["hash"] ?? '';
$key           = $_POST["key"] ?? '';
$productinfo   = $_POST["productinfo"] ?? '';
$email         = $_POST["email"] ?? '';
$orderId       = $_POST["udf1"] ?? '';



// ==============================
// HASH VERIFY
// ==============================
$udf1 = $_POST['udf1'] ?? '';
$udf2 = $_POST['udf2'] ?? '';
$udf3 = $_POST['udf3'] ?? '';
$udf4 = $_POST['udf4'] ?? '';
$udf5 = $_POST['udf5'] ?? '';

$hashSeq = $payuSalt . "|" . $status . "||||||" . 
           $udf5 . "|" . $udf4 . "|" . $udf3 . "|" . $udf2 . "|" . $udf1 . "|" . 
           $email . "|" . $firstname . "|" . $productinfo . "|" . $amount . "|" . $txnid . "|" . $key;

$calculated_hash = strtolower(hash("sha512", $hashSeq));

// ==============================
// DEFAULT MESSAGE
// ==============================
$message = "Payment Verification Failed ❌";
$isSuccess = false;

// ==============================
// VALIDATE PAYMENT
// ==============================
if ($calculated_hash === $posted_hash && $status === "success") {

    $isSuccess = true;
    $message = "Payment Successful ✅";

    if (!empty($orderId)) {
        $stmt = $conn->prepare("
            UPDATE plan_users 
            SET transaction_status = 'SUCCESS', 
                razorpay_order_id = ? 
            WHERE order_number = ?
        ");
        $stmt->bind_param("ss", $txnid, $orderId);
        $stmt->execute();
        $stmt->close();
    }

} else {
    $message = "Payment Failed or Tampered ❌";
}

// ==============================
// FETCH ORDER DETAILS
// ==============================
$orderData = null;
$type = '';
$type_id = '';

if (!empty($orderId)) {

    $stmt = $conn->prepare("SELECT * FROM plan_users WHERE order_number = ?");
    $stmt->bind_param("s", $orderId);
    $stmt->execute();

    $result = $stmt->get_result();
    $orderData = $result->fetch_assoc();
    $stmt->close();

    $type = 'subscription';
    $type_id = $orderData['subscription_id'] ?? '';
}



$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment Status</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5 text-center">

    <h3 class="<?= $isSuccess ? 'text-success' : 'text-danger' ?>">
        <?= $message ?>
    </h3>

    <p class="text-muted">Redirecting to app in 5 seconds...</p>

    <div class="card mt-4 p-4 shadow text-start">

        <h5>Payment Info</h5>
        <p><strong>Transaction ID:</strong> <?= htmlspecialchars($txnid) ?></p>
        <p><strong>Amount:</strong> ₹<?= htmlspecialchars($amount) ?></p>
        <p><strong>Name:</strong> <?= htmlspecialchars($firstname) ?></p>

    </div>

</div>





<!-- ==============================
     REDIRECT SCRIPT
============================== -->
<script>
    setTimeout(function () {

        var type = "<?= $type ?>";
        var type_id = "<?= $type_id ?>";

            window.location.href = "dainiknirman://payment/success?transaction_id=<?= $txnid ?>&type=" + type + "&item_id=" + type_id;
        
            // Close the browser after a short delay
            setTimeout(() => {
                window.close();
            }, 500);

    }, 5000);
</script>

</body>
</html>