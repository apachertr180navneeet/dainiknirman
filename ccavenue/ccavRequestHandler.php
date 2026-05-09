<?php

require_once __DIR__ . '/Crypto.php';
require_once __DIR__ . '/../db_config.php';

use CCAvenue\Crypto;

$merchant_id = '4442439';
$access_code = 'AVKL92NE20AO29LKOA';
$working_key = '5A0CF9572A5DDBDAC144DC29B3995593';
$ccavenue_url = 'https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction';

// Basic Validation
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Error: Invalid request method. This page requires a POST submission from the checkout form.");
}

if (!isset($_POST['order_id']) || $_POST['order_id'] === '' || !isset($_POST['prod_id']) || $_POST['prod_id'] === '') {
    die("Error: Missing mandatory parameters (Order ID or Product ID).");
}

// Collect data from POST
$order_id = $_POST['order_id'] ?? '';
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
$payment_option = $_POST['payment_option'] ?? '';

$bookStmt = $conn->prepare("SELECT book_name, author_name, price FROM books WHERE id = ?");
$bookStmt->bind_param("i", $prod_id);
$bookStmt->execute();
$bookResult = $bookStmt->get_result();
$bookData = $bookResult->fetch_assoc();
$bookStmt->close();

if (!$bookData) {
    die("Error: Product not found.");
}

$amount = number_format((float) $bookData['price'], 2, '.', '');

if ((float) $amount <= 0) {
    die("Error: Invalid product amount.");
}

// Build merchant data array
$data = [
    'merchant_id' => $merchant_id,
    'order_id' => $order_id,
    'amount' => $amount,
    'currency' => 'INR',
    'redirect_url' => $redirect_url,
    'cancel_url' => $cancel_url,
    'billing_name' => $name,
    'billing_address' => $address,
    'billing_city' => $city,
    'billing_state' => $state,
    'billing_zip' => $zip,
    'billing_country' => $country,
    'billing_tel' => $phone,
    'billing_email' => $email,
];

if (in_array($payment_option, ['OPTUPI'], true)) {
    $data['payment_option'] = $payment_option;
}

$merchant_data = '';
foreach ($data as $key => $value) {
    $merchant_data .= $key . '=' . $value . '&';
}
$merchant_data = rtrim($merchant_data, '&');

// Encrypt the data
$encrypted_data = Crypto::encrypt($merchant_data, $working_key);

// Record the order in the database (PENDING status)
if ($conn && $order_id && $user_id && $prod_id) {
    try {
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
    <form method="POST" action="<?= htmlspecialchars($ccavenue_url) ?>">
        <input type="hidden" name="encRequest" value="<?= htmlspecialchars($encrypted_data) ?>">
        <input type="hidden" name="access_code" value="<?= htmlspecialchars($access_code) ?>">
        <input type="hidden" name="merchant_id" value="<?= htmlspecialchars($merchant_id) ?>">
    </form>
</body>
</html>
