<?php

require_once __DIR__ . '/Crypto.php';
require_once __DIR__ . '/../db_config.php';

use CCAvenue\Crypto;

$working_key = '5A0CF9572A5DDBDAC144DC29B3995593';
$access_code = 'AVKL92NE20AO29LKOA';

$encResponse = $_POST['encResp'] ?? '';
$decResponse = Crypto::decrypt($encResponse, $working_key);

parse_str($decResponse, $data);

$order_id = $data['order_id'] ?? '';
$order_status = $data['order_status'] ?? '';
$transaction_id = $data['tracking_id'] ?? '';
$amount = $data['amount'] ?? '';
$failure_message = $data['failure_message'] ?? '';

// Update order status in database
if ($conn && $order_id) {
    $dbStatus = 'FAILED';
    if ($order_status === 'Success') {
        $dbStatus = 'SUCCESS';
    } elseif ($order_status === 'Aborted') {
        $dbStatus = 'CANCELED';
    }

    $paymentDetails = json_encode($data);

    $updateStmt = $conn->prepare("UPDATE orders SET transaction_status = ?, payment_details = ?, updated_at = NOW() WHERE order_number = ?");
    $updateStmt->bind_param("sss", $dbStatus, $paymentDetails, $order_id);
    $updateStmt->execute();
    $updateStmt->close();
}

if ($order_status === 'Success') {
    header('Location: ../thankyou.html?order_id=' . urlencode($order_id) . '&status=success');
} elseif ($order_status === 'Aborted') {
    header('Location: ../checkout?error=payment_aborted&status=' . urlencode($order_status));
} else {
    // Pass the failure message and status to help debugging
    header('Location: ../checkout?error=payment_failed&status=' . urlencode($order_status) . '&msg=' . urlencode($failure_message));
}
exit;
