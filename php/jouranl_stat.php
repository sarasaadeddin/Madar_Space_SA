<?php
header("Content-Type: application/json");

session_start();

include "connect.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "totalEntries" => 0,
        "tagStats" => [
            "ideas" => 0,
            "work" => 0,
            "personal" => 0,
            "reminders" => 0
        ]
    ]);
    exit;
}

$user_id = $_SESSION['user_id'];


$totalQuery = $conn->prepare(
    "SELECT COUNT(*) as total FROM journal WHERE user_id = ?"
);
$totalQuery->bind_param("i", $user_id);
$totalQuery->execute();
$totalResult = $totalQuery->get_result();
$totalData = $totalResult->fetch_assoc();
$totalEntries = $totalData['total'];

// Get entries by tag
$tagQuery = $conn->prepare(
    "SELECT tag, COUNT(*) as count FROM journal WHERE user_id = ? GROUP BY tag"
);
$tagQuery->bind_param("i", $user_id);
$tagQuery->execute();
$tagResult = $tagQuery->get_result();

$tagStats = [
    "ideas" => 0,
    "work" => 0,
    "personal" => 0,
    "reminders" => 0
];

while ($row = $tagResult->fetch_assoc()) {
    if (isset($tagStats[$row['tag']])) {
        $tagStats[$row['tag']] = $row['count'];
    }
}


$tagPercentages = [];
foreach ($tagStats as $tag => $count) {
    $percentage = $totalEntries > 0 ? round(($count / $totalEntries) * 100) : 0;
    $tagPercentages[$tag] = [
        "count" => $count,
        "percentage" => $percentage
    ];
}

echo json_encode([
    "totalEntries" => $totalEntries,
    "tagStats" => $tagPercentages
]);

$totalQuery->close();
$tagQuery->close();
$conn->close();
?>