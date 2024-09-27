<?php
session_start();
include('db.php');

if (!isset($_SESSION['username'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit();
}

$username = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    switch ($action) {
        case 'start_match':
            startMatch($conn, $username);
            break;
        case 'check_match':
            checkMatch($conn, $username);
            break;
        case 'get_letter':
            getLetter($conn, $username, $_POST['match_id']);
            break;
        case 'submit_time':
            submitTime($conn, $username, $_POST['match_id']);
            break;
        case 'check_result':
            checkResult($conn, $username, $_POST['match_id']);
            break;
        case 'cancel_match':
            cancelMatch($conn, $username);
            break;
        case 'update_time':
            updateTime($conn, $username, $_POST['match_id']);
            break;
        default:
            echo json_encode(['error' => 'Invalid action']);
    }
}

function startMatch($conn, $username) {
    $stmt = $conn->prepare("SELECT id FROM setuna_matches WHERE status = 'waiting' AND player1 != ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $match_id = $row['id'];
        $stmt = $conn->prepare("UPDATE setuna_matches SET player2 = ?, status = 'in_progress' WHERE id = ?");
        $stmt->bind_param("si", $username, $match_id);
        $stmt->execute();
        echo json_encode(['status' => 'matched', 'match_id' => $match_id]);
    } else {
        $stmt = $conn->prepare("INSERT INTO setuna_matches (player1, status) VALUES (?, 'waiting')");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        echo json_encode(['status' => 'waiting']);
    }
}

function checkMatch($conn, $username) {
    $stmt = $conn->prepare("SELECT id FROM setuna_matches WHERE (player1 = ? OR player2 = ?) AND status = 'in_progress'");
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode(['status' => 'matched', 'match_id' => $row['id']]);
    } else {
        echo json_encode(['status' => 'waiting']);
    }
}

function getLetter($conn, $username, $match_id) {
    $stmt = $conn->prepare("SELECT letter FROM setuna_matches WHERE id = ?");
    $stmt->bind_param("i", $match_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row['letter']) {
        $letter = chr(rand(97, 122));
        $stmt = $conn->prepare("UPDATE setuna_matches SET letter = ?, start_time = NOW() + INTERVAL 3 SECOND WHERE id = ?");
        $stmt->bind_param("si", $letter, $match_id);
        $stmt->execute();
    } else {
        $letter = $row['letter'];
    }

    echo json_encode(['letter' => $letter]);
}

function submitTime($conn, $username, $match_id) {
    $stmt = $conn->prepare("SELECT start_time, letter, player1, player2, player1_time, player2_time FROM setuna_matches WHERE id = ?");
    $stmt->bind_param("i", $match_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $start_time = strtotime($row['start_time']);
    $current_time = microtime(true);
    $reaction_time = max(0, $current_time - $start_time);

    if ($username === $row['player1']) {
        $stmt = $conn->prepare("UPDATE setuna_matches SET player1_time = ? WHERE id = ? AND player1_time IS NULL");
        $stmt->bind_param("di", $reaction_time, $match_id);
    } elseif ($username === $row['player2']) {
        $stmt = $conn->prepare("UPDATE setuna_matches SET player2_time = ? WHERE id = ? AND player2_time IS NULL");
        $stmt->bind_param("di", $reaction_time, $match_id);
    }
    $stmt->execute();

    echo json_encode(['your_time' => number_format($reaction_time, 5), 'letter' => $row['letter']]);
}

function checkResult($conn, $username, $match_id) {
    $stmt = $conn->prepare("SELECT player1, player2, player1_time, player2_time, status FROM setuna_matches WHERE id = ?");
    $stmt->bind_param("i", $match_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['player1_time'] !== null && $row['player2_time'] !== null) {
        $winner = determineWinner($row['player1_time'], $row['player2_time'], $row['player1'], $row['player2']);
        $stmt = $conn->prepare("UPDATE setuna_matches SET status = 'completed', winner = ? WHERE id = ?");
        $stmt->bind_param("si", $winner, $match_id);
        $stmt->execute();
        $opponent_time = $username === $row['player1'] ? $row['player2_time'] : $row['player1_time'];
        echo json_encode(['status' => 'completed', 'winner' => $winner, 'opponent_time' => number_format($opponent_time, 5)]);
    } else {
        echo json_encode(['status' => 'in_progress']);
    }
}

function determineWinner($time1, $time2, $player1, $player2) {
    if ($time1 < $time2) {
        return $player1;
    } elseif ($time2 < $time1) {
        return $player2;
    } else {
        return 'draw';
    }
}

function cancelMatch($conn, $username) {
    $stmt = $conn->prepare("DELETE FROM setuna_matches WHERE (player1 = ? OR player2 = ?) AND status = 'waiting'");
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    echo json_encode(['status' => 'cancelled']);
}

function updateTime($conn, $username, $match_id) {
    $stmt = $conn->prepare("SELECT start_time, player1, player2, player1_time, player2_time FROM setuna_matches WHERE id = ?");
    $stmt->bind_param("i", $match_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $start_time = strtotime($row['start_time']);
    $current_time = microtime(true);
    $elapsed_time = max(0, $current_time - $start_time);

    if ($username === $row['player1'] && $row['player1_time'] === null) {
        $stmt = $conn->prepare("UPDATE setuna_matches SET player1_time = ? WHERE id = ? AND player1_time IS NULL");
        $stmt->bind_param("di", $elapsed_time, $match_id);
    } elseif ($username === $row['player2'] && $row['player2_time'] === null) {
        $stmt = $conn->prepare("UPDATE setuna_matches SET player2_time = ? WHERE id = ? AND player2_time IS NULL");
        $stmt->bind_param("di", $elapsed_time, $match_id);
    }
    $stmt->execute();

    echo json_encode(['status' => 'updated']);
}