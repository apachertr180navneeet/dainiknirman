<?php

require __DIR__ . '/db_config.php';

$userid = $_GET['user_id'] ?? null;

if (!$userid) {
    header('Location: https://dainiknirman.com/');
    exit;
}

$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $userid);
$stmt->execute();
$stmt->close();
$conn->close();

header('Location: https://dainiknirman.com/?message=user_deleted');
exit;
