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
$completed = $_POST["completed"] ?? "";

if ($id === "" || $completed === "") {
    echo json_encode([
        "success" => false,
        "message" => "Missing data"
    ]);
    exit;
}

$stmt = $conn->prepare(
    "UPDATE todo
     SET completed = ?
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

$stmt->bind_param("iii", $completed, $id, $user_id);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Task updated successfully"
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Failed to update task",
        "error" => $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>