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

$journals = [];

while ($row = $result->fetch_assoc()) {
    $journals[] = [
        "id" => (int)$row["id"],
        "title" => $row["title"],
        "content" => $row["content"],
        "tag" => $row["tag"],
        "created_at" => $row["created_at"]
    ];
}

echo json_encode($journals);

$stmt->close();
$conn->close();
?>