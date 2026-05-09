<?php

require_once __DIR__ . '/Crypto.php';
require_once __DIR__ . '/../db_config.php';

use CCAvenue\Crypto;

$working_key = '5A0CF9572A5DDBDAC144DC29B3995593';
$access_code = 'AVKL92NE20AO29LKOA';

$encResponse = $_POST['encResp'] ?? '';

if ($encResponse === '') {
    header('Location: ../index.html?error=missing_gateway_response');
    exit;
}

$decResponse = Crypto::decrypt($encResponse, $working_key);

if (!$decResponse) {
    header('Location: ../index.html?error=invalid_gateway_response');
    exit;
}

parse_str($decResponse, $data);

$order_id = $data['order_id'] ?? '';
$order_status = $data['order_status'] ?? '';
$transaction_id = $data['tracking_id'] ?? '';
$amount = $data['amount'] ?? '';
$failure_message = $data['failure_message'] ?? '';
$checkoutUrl = '../index.html?error=payment_failed';
$amountMatches = false;

// Update order status in database
if ($conn && $order_id) {
    $orderStmt = $conn->prepare("
        SELECT o.id, o.user_id, o.amount, od.type_id AS prod_id
        FROM orders o
        LEFT JOIN order_details od ON od.order_id = o.id AND od.type = 'BOOK'
        WHERE o.order_number = ?
        LIMIT 1
    ");
    $orderStmt->bind_param("s", $order_id);
    $orderStmt->execute();
    $orderResult = $orderStmt->get_result();
    $orderData = $orderResult->fetch_assoc();
    $orderStmt->close();

    if ($orderData) {
        $checkoutUrl = '../checkout?user_id=' . urlencode($orderData['user_id']) . '&prod_id=' . urlencode($orderData['prod_id']);
    }

    $dbStatus = 'FAILED';
    $amountMatches = $orderData && abs((float) $orderData['amount'] - (float) $amount) < 0.01;

    if ($order_status === 'Success' && $amountMatches) {
        $dbStatus = 'SUCCESS';
    } elseif ($order_status === 'Aborted') {
        $dbStatus = 'CANCELED';
    }

    if ($order_status === 'Success' && !$amountMatches) {
        $data['local_error'] = 'Gateway amount does not match order amount.';
    }

    $paymentDetails = json_encode($data);

    $updateStmt = $conn->prepare("UPDATE orders SET transaction_status = ?, payment_details = ?, updated_at = NOW() WHERE order_number = ?");
    $updateStmt->bind_param("sss", $dbStatus, $paymentDetails, $order_id);
    $updateStmt->execute();
    $updateStmt->close();
}

if ($order_status === 'Success' && $amountMatches) {
    header('Location: ../thankyou.html?order_id=' . urlencode($order_id) . '&status=success');
} elseif ($order_status === 'Aborted') {
    header('Location: ' . $checkoutUrl . '&error=payment_aborted&status=' . urlencode($order_status));
} else {
    // Pass the failure message and status to help debugging
    header('Location: ' . $checkoutUrl . '&error=payment_failed&status=' . urlencode($order_status) . '&msg=' . urlencode($failure_message));
}
exit;
