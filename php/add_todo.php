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
$task = $_POST["task"] ?? "";

$task = trim($task);

if ($task === "") {
    echo json_encode([
        "success" => false,
        "message" => "Task is required"
    ]);
    exit;
}

$completed = 0;

$stmt = $conn->prepare(
    "INSERT INTO todo (user_id, task, completed) VALUES (?, ?, ?)"
);

if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => "Prepare failed",
        "error" => $conn->error
    ]);
    exit;
}

$stmt->bind_param("isi", $user_id, $task, $completed);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Task added successfully"
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Failed to add task",
        "error" => $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>