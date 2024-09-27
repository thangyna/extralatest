<?php
session_start();
require_once '../db.php';

header('Content-Type: application/json');

$game_id = $_POST['game_id'];

$stmt = $conn->prepare("SELECT * FROM setunanomikiri_matches WHERE id = ?");
$stmt->bind_param("i", $game_id);
$stmt->execute();
$result = $stmt->get_result();
$game = $result->fetch_assoc();

if ($game['letter'] === null) {
    $letter = chr(rand(65, 90));
    $stmt = $conn->prepare("UPDATE setunanomikiri_matches SET letter = ? WHERE id = ?");
    $stmt->bind_param("si", $letter, $game_id);
    $stmt->execute();
    echo json_encode(['success' => true, 'letter' => $letter]);
} else {
    echo json_encode(['success' => false]);
}