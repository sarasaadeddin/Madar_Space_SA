<?php
header("Content-Type: application/json");

session_start();

include "connect.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare(
    "SELECT id, title, content, tag, created_at 
     FROM journal 
     WHERE user_id = ? 
     ORDER BY created_at DESC"
);

if (!$stmt) {
    echo json_encode([]);
    exit;
}

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

$journals = [];

while ($row = $result->fetch_assoc()) {
    $journals[] = $row;
}

echo json_encode($journals);

$stmt->close();
$conn->close();
?>