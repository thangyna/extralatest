<?php
session_start();
require_once '../db.php';

header('Content-Type: application/json');

$game_id = $_POST['game_id'];
$letter = $_POST['letter'];
$reaction_time = $_POST['reaction_time'];
$username = $_SESSION['username'];

$stmt = $conn->prepare("SELECT * FROM setunanomikiri_matches WHERE id = ?");
$stmt->bind_param("i", $game_id);
$stmt->execute();
$result = $stmt->get_result();
$game = $result->fetch_assoc();

if ($game['letter'] !== $letter) {
    echo json_encode(['success' => false]);
    exit();
}

if ($game['player1'] === $username) {
    $stmt = $conn->prepare("UPDATE setunanomikiri_matches SET player1_time = ? WHERE id = ?");
    $stmt->bind_param("di", $reaction_time, $game_id);
    $stmt->execute();
} else {
    $stmt = $conn->prepare("UPDATE setunanomikiri_matches SET player2_time = ? WHERE id = ?");
    $stmt->bind_param("di", $reaction_time, $game_id);
    $stmt->execute();
}

$stmt = $conn->prepare("SELECT player1_time, player2_time, player1, player2 FROM setunanomikiri_matches WHERE id = ?");
$stmt->bind_param("i", $game_id);
$stmt->execute();
$result = $stmt->get_result();
$game = $result->fetch_assoc();

if ($game['player1_time'] !== null && $game['player2_time'] !== null) {
    if ($game['player1_time'] < $game['player2_time']) {
        $winner = $game['player1'];
        $reaction_time = $game['player1_time'];
    } else {
        $winner = $game['player2'];
        $reaction_time = $game['player2_time'];
    }
    $stmt = $conn->prepare("UPDATE setunanomikiri_matches SET winner = ?, status = 'finished', reaction_time = ? WHERE id = ?");
    $stmt->bind_param("sdi", $winner, $reaction_time, $game_id);
    $stmt->execute();
}

echo json_encode(['success' => true]);