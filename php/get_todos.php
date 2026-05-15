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
    "SELECT id, task, completed, created_at
     FROM todo
     WHERE user_id = ? AND DATE(created_at) = CURDATE()
     ORDER BY created_at DESC"
);

if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => "Prepare failed",
        "error" => $conn->error
    ]);
    exit;
}

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

$todos = [];

while ($row = $result->fetch_assoc()) {
    $todos[] = [
        "id" => (int)$row["id"],
        "task" => $row["task"],
        "completed" => (int)$row["completed"],
        "created_at" => $row["created_at"]
    ];
}

echo json_encode($todos);

$stmt->close();
$conn->close();
?>