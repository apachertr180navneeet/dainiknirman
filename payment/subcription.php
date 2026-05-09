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
// DB CONNECTION CHECK
// ==============================
if ($conn->connect_error) {
    $message = '<div id="alertBox" class="alert alert-danger">Database Error: '.$conn->connect_error.'</div>';
}

// ==============================
// GET ORDER ID
// ==============================
$orderId = trim($_GET['order_id'] ?? '');

// ==============================
// VALIDATION
// ==============================
if (!$message) {

    if (empty($orderId)) {

        $message = '<div id="alertBox" class="alert alert-danger">Invalid Order ID</div>';

    } else {

        // ==============================
        // FETCH ORDER + USER
        // ==============================
        $stmt = $conn->prepare("
            SELECT 
                pu.*, 
                u.name AS user_name,
                u.mobile AS user_mobile
            FROM plan_users pu
            LEFT JOIN users u ON u.id = pu.user_id
            WHERE pu.id = ?
        ");

        if (!$stmt) {

            $message = '<div id="alertBox" class="alert alert-danger">SQL Error: '.$conn->error.'</div>';

        } else {

            $stmt->bind_param("s", $orderId);

            if ($stmt->execute()) {

                $result = $stmt->get_result();

                if ($result && $result->num_rows > 0) {

                    $order = $result->fetch_assoc();
                    $message = '<div id="alertBox" class="alert alert-success">Order Found Successfully</div>';

                    // ==============================
                    // FETCH ORDER ITEMS (OPTIONAL)
                    // ==============================
                    $detailStmt = $conn->prepare("
                        SELECT * FROM plan_users WHERE id = ?
                    ");


                    if ($detailStmt) {
                        $detailStmt->bind_param("i", $order['id']);
                        $detailStmt->execute();
                        $detailResult = $detailStmt->get_result();

                        while ($row = $detailResult->fetch_assoc()) {
                            $details[] = $row;
                        }

                        $detailStmt->close();
                    }

                } else {

                    $message = '<div id="alertBox" class="alert alert-warning">Order Not Found</div>';
                }

            } else {

                $message = '<div id="alertBox" class="alert alert-danger">Execute Error: '.$stmt->error.'</div>';
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
    <title>Order Details</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .fade-out {
            opacity: 0;
            transition: opacity 0.5s ease-out;
        }
    </style>
</head>
<body>

<div class="container mt-5">

    <h3 class="mb-4">Order Check Page</h3>

    <!-- MESSAGE -->
    <?php echo $message; ?>

    <!-- ORDER DETAILS -->
    <?php if (!empty($order)) { ?>

        <div class="card mt-4 shadow">
            <div class="card-body">

                <h5 class="card-title mb-3">Order Details</h5>

                <p><strong>Order ID:</strong> <?= htmlspecialchars($order['order_number']) ?></p>

                <p><strong>Customer Name:</strong> 
                    <?= htmlspecialchars($order['user_name'] ?? 'N/A') ?>
                </p>

                <p><strong>Mobile:</strong> 
                    <?= htmlspecialchars($order['user_mobile'] ?? 'N/A') ?>
                </p>

                <p><strong>Amount:</strong> 
                    ₹<?= htmlspecialchars($order['subscription_amount'] ?? '0') ?>
                </p>

                <p><strong>Status:</strong>
                    <?php
                        $status = strtolower($order['status'] ?? 'pending');

                        $badgeClass = match ($status) {
                            'success' => 'bg-success',
                            'failed'  => 'bg-danger',
                            default   => 'bg-warning'
                        };
                    ?>
                    <span class="badge <?= $badgeClass ?>">
                        <?= ucfirst($status) ?>
                    </span>
                </p>

                <p><strong>Date:</strong> 
                    <?= htmlspecialchars($order['created_at'] ?? '') ?>
                </p>

            </div>
        </div>

    <?php } ?>

</div>

<!-- AUTO HIDE ALERT -->
<script>
setTimeout(function () {
    let alertBox = document.getElementById('alertBox');
    if (alertBox) {
        alertBox.classList.add('fade-out');
        setTimeout(() => alertBox.remove(), 500);
    }
}, 2000);
</script>

<!-- REDIRECT ONLY IF NOT SUCCESS -->
<?php if (!empty($order) && strtolower($order['transaction_status']) !== 'success') { ?>
<script>
setTimeout(function () {
    window.location.href = "subpay.php?order_id=<?= urlencode($orderId) ?>";
}, 5000);
</script>
<?php } ?>

</body>
</html>