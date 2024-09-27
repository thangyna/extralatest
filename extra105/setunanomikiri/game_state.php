<?php
session_start();
require_once '../db.php';

header('Content-Type: application/json');

$game_id = $_GET['game_id'];
$username = $_SESSION['username'];

$stmt = $conn->prepare("SELECT * FROM setunanomikiri_matches WHERE id = ?");
$stmt->bind_param("i", $game_id);
$stmt->execute();
$result = $stmt->get_result();
$game = $result->fetch_assoc();

if ($game['status'] == 'waiting') {
    echo json_encode(['state' => 'waiting']);
} elseif ($game['status'] == 'matched') {
    if ($game['letter'] === null) {
        echo json_encode(['state' => 'matched']);
    } else {
        echo json_encode(['state' => 'letter', 'letter' => $game['letter']]);
    }
} elseif ($game['status'] == 'finished') {
    echo json_encode([
        'state' => 'result', 
        'winner' => $game['winner'],
        'reaction_time' => $game['reaction_time']
    ]);
} else {
    echo json_encode(['state' => 'waiting']);
}