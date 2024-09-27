<?php
session_start();
require_once '../db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['username'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

$username = $_SESSION['username'];

try {
    $conn->begin_transaction();

    $stmt = $conn->prepare("SELECT id FROM setunanomikiri_matches WHERE status = 'waiting' AND player1 != ? LIMIT 1 FOR UPDATE");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $match = $result->fetch_assoc();
        $stmt = $conn->prepare("UPDATE setunanomikiri_matches SET player2 = ?, status = 'matched' WHERE id = ?");
        $stmt->bind_param("si", $username, $match['id']);
        $stmt->execute();
        $match_id = $match['id'];
    } else {
        $stmt = $conn->prepare("INSERT INTO setunanomikiri_matches (player1, status) VALUES (?, 'waiting')");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $match_id = $conn->insert_id;
    }

    $conn->commit();
    echo json_encode(['match_id' => $match_id]);
} catch (Exception $e) {
    $conn->rollback();
    error_log($e->getMessage());
    echo json_encode(['error' => 'An error occurred. Please try again later.']);
}