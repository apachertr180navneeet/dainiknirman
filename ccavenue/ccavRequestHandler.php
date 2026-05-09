<?php

require_once __DIR__ . '/Crypto.php';
require_once __DIR__ . '/../db_config.php';

use CCAvenue\Crypto;

$merchant_id = '4442439';
$access_code = 'AVHS92NE07AJ41SHJA';
$working_key = 'E2059F4553269CE76A03F561109D20E8';

// Basic Validation
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Error: Invalid request method. This page requires a POST submission from the checkout form.");
}

if (!isset($_POST['order_id']) || $_POST['order_id'] === '' || !isset($_POST['amount']) || $_POST['amount'] === '') {
    die("Error: Missing mandatory parameters (Order ID or Amount). Received Order ID: " . ($_POST['order_id'] ?? 'NULL') . ", Amount: " . ($_POST['amount'] ?? 'NULL'));
}

// Collect data from POST
$order_id = $_POST['order_id'] ?? '';
$amount = $_POST['amount'] ?? '';
$name = $_POST['billing_name'] ?? '';
$address = $_POST['billing_address'] ?? '';
$city = $_POST['billing_city'] ?? '';
$state = $_POST['billing_state'] ?? '';
$zip = $_POST['billing_zip'] ?? '';
$country = $_POST['billing_country'] ?? '';
$phone = $_POST['billing_tel'] ?? '';
$email = $_POST['billing_email'] ?? '';
$redirect_url = $_POST['redirect_url'] ?? '';
$cancel_url = $_POST['cancel_url'] ?? '';
$user_id = $_POST['user_id'] ?? '';
$prod_id = $_POST['prod_id'] ?? '';

// Build merchant data string for CCAvenue
$merchant_data = "merchant_id=$merchant_id&order_id=$order_id&amount=$amount&currency=INR&redirect_url=$redirect_url&cancel_url=$cancel_url&billing_name=$name&billing_address=$address&billing_city=$city&billing_state=$state&billing_zip=$zip&billing_country=$country&billing_tel=$phone&billing_email=$email";

// Encrypt the data
$encrypted_data = Crypto::encrypt($merchant_data, $working_key);

// Record the order in the database (PENDING status)
if ($conn && $order_id && $user_id && $prod_id) {
    try {
        // Fetch book details
        $bookStmt = $conn->prepare("SELECT book_name, author_name FROM books WHERE id = ?");
        $bookStmt->bind_param("i", $prod_id);
        $bookStmt->execute();
        $bookResult = $bookStmt->get_result();
        $bookData = $bookResult->fetch_assoc();
        $bookStmt->close();

        $itemDetails = json_encode([
            'book_name' => $bookData['book_name'] ?? 'Unknown Book',
            'author_name' => $bookData['author_name'] ?? 'Unknown Author',
            'price' => $amount
        ]);

        // Insert into orders table
        $orderStmt = $conn->prepare("INSERT INTO orders (user_id, order_number, amount, payment_mode, payment_gateway, transaction_status, created_at, updated_at, total_items) VALUES (?, ?, ?, 'ONLINE', 'CCAVENUE', 'PENDING', NOW(), NOW(), 1)");
        $orderStmt->bind_param("isd", $user_id, $order_id, $amount);
        $orderStmt->execute();
        $dbOrderId = $conn->insert_id;
        $orderStmt->close();

        // Insert into order_details table
        if ($dbOrderId) {
            $detailStmt = $conn->prepare("INSERT INTO order_details (order_id, type, type_id, item_details, amount, created_at, updated_at) VALUES (?, 'BOOK', ?, ?, ?, NOW(), NOW())");
            $detailStmt->bind_param("iisd", $dbOrderId, $prod_id, $itemDetails, $amount);
            $detailStmt->execute();
            $detailStmt->close();
        }
    } catch (Exception $e) {
        // Log error but don't stop the payment process
        error_log("Order creation failed: " . $e->getMessage());
    }
}
?>
<html>
<body onload="document.forms[0].submit()">
    <form method="POST" action="https://test.ccavenue.com/transaction/transaction.do?command=initiateTransaction">
        <input type="hidden" name="encRequest" value="<?= htmlspecialchars($encrypted_data) ?>">
        <input type="hidden" name="access_code" value="<?= htmlspecialchars($access_code) ?>">
    </form>
</body>
</html>
