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

$title = $_POST["title"] ?? "";
$content = $_POST["content"] ?? "";
$tag = $_POST["tag"] ?? "";

$title = trim($title);
$content = trim($content);
$tag = trim($tag);

if ($title === "" || $content === "" || $tag === "") {
    echo json_encode([
        "success" => false,
        "message" => "Missing fields"
    ]);
    exit;
}

$stmt = $conn->prepare(
    "INSERT INTO journal (user_id, title, content, tag) VALUES (?, ?, ?, ?)"
);

if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => "Prepare failed",
        "error" => $conn->error
    ]);
    exit;
}

$stmt->bind_param("isss", $user_id, $title, $content, $tag);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Journal saved successfully"
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Failed to save journal",
        "error" => $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>