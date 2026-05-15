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

if ($id === "") {
    echo json_encode([
        "success" => false,
        "message" => "Missing journal id"
    ]);
    exit;
}

$stmt = $conn->prepare(
    "DELETE FROM journal
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

$stmt->bind_param("ii", $id, $user_id);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Journal deleted successfully"
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Failed to delete journal",
        "error" => $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>