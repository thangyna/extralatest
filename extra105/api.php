<?php
header('Content-Type: application/json');

if (!isset($_GET['hiragana']) || !isset($_GET['current_key']) || !isset($_GET['key_history'])) {
    echo json_encode(['error' => 'Invalid parameters']);
    exit();
}

$hiragana = $_GET['hiragana'];
$current_key = $_GET['current_key'];
$key_history = $_GET['key_history'];

$apiUrl = "https://typingapi.com/api/v1/?hiragana=" . urlencode($hiragana) . "&t_key=" . urlencode($current_key) . "&key_history=" . urlencode($key_history);

$response = file_get_contents($apiUrl);

if ($response === FALSE) {
    echo json_encode(['error' => 'Failed to fetch data from API']);
    exit();
}

echo $response;
?>
