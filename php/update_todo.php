<?php
header("Content-Type: application/json");

session_start();

include "connect.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "User not logged in"
    ]);
    exit;
}

$user_id = $_SESSION['user_id'];
$id = $_POST["id"] ?? "";
$task = $_POST["task"] ?? "";

$task = trim($task);

if ($id === "" || $task === "") {
    echo json_encode([
        "success" => false,
        "message" => "Missing data"
    ]);
    exit;
}

$stmt = $conn->prepare(
    "UPDATE todo
     SET task = ?
     WHERE id = ? AND user_id = ?"
);

if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => "Prepare failed",
        "error" => $conn->error
    ]);
    exit;
}

$stmt->bind_param("sii", $task, $id, $user_id);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Task edited successfully"
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Failed to edit task",
        "error" => $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>
