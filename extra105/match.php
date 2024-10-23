<?php
session_start();
require_once('db.php');

// エラー表示を無効にし、ログに記録する
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

try {
    if (!isset($_SESSION['username'])) {
        throw new Exception('Not logged in');
    }

    $username = $_SESSION['username'];

    if ($_POST['action'] === 'start_match') {
        // 空いている部屋を探す（自分以外のプレイヤーの部屋）
        $stmt = $pdo->prepare("SELECT id FROM matches WHERE player2 IS NULL AND status = 'waiting' AND player1 != ? LIMIT 1");
        $stmt->execute([$username]);
        $room = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($room) {
            // 空いている部屋に参加
            $stmt = $pdo->prepare("UPDATE matches SET player2 = ?, status = 'ready' WHERE id = ?");
            $stmt->execute([$username, $room['id']]);
            echo json_encode(['status' => 'joined', 'match_id' => $room['id']]);
        } else {
            // 新しい部屋を作成
            $stmt = $pdo->prepare("INSERT INTO matches (player1, status) VALUES (?, 'waiting')");
            $stmt->execute([$username]);
            $matchId = $pdo->lastInsertId();
            echo json_encode(['status' => 'created', 'match_id' => $matchId]);
        }
    } elseif ($_POST['action'] === 'check_match') {
        $matchId = $_POST['match_id'];
        $stmt = $pdo->prepare("SELECT player1, player2, status FROM matches WHERE id = ?");
        $stmt->execute([$matchId]);
        $match = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($match) {
            if ($match['player2'] !== null) {
                // player2 が参加したら、ステータスを 'ready' に更新
                $stmt = $pdo->prepare("UPDATE matches SET status = 'ready' WHERE id = ?");
                $stmt->execute([$matchId]);
                $opponent = ($match['player1'] === $username) ? $match['player2'] : $match['player1'];
                echo json_encode(['status' => 'ready', 'match_id' => $matchId, 'opponent' => $opponent]);
            } else {
                echo json_encode(['status' => 'waiting']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Match not found']);
        }
    } elseif ($_POST['action'] === 'get_letter') {
        $matchId = $_POST['match_id'];
        $letter = chr(rand(97, 122)); // a-z のランダムな文字

        $stmt = $pdo->prepare("UPDATE matches SET letter = ?, status = 'active' WHERE id = ?");
        $stmt->execute([$letter, $matchId]);

        echo json_encode(['status' => 'success', 'letter' => $letter]);
    } elseif ($_POST['action'] === 'submit_time') {
        $matchId = $_POST['match_id'];
        $reactionTime = floatval($_POST['reaction_time']);
        
        $stmt = $pdo->prepare("UPDATE matches SET player1_time = CASE WHEN player1 = ? THEN ? ELSE player1_time END, player2_time = CASE WHEN player2 = ? THEN ? ELSE player2_time END WHERE id = ?");
        $stmt->execute([$username, $reactionTime, $username, $reactionTime, $matchId]);

        echo json_encode(['status' => 'success']);
    } elseif ($_POST['action'] === 'check_result') {
        $matchId = $_POST['match_id'];

        $stmt = $pdo->prepare("SELECT player1, player2, player1_time, player2_time FROM matches WHERE id = ?");
        $stmt->execute([$matchId]);
        $match = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($match) {
            $yourTime = ($match['player1'] === $username) ? $match['player1_time'] : $match['player2_time'];
            $opponentTime = ($match['player1'] === $username) ? $match['player2_time'] : $match['player1_time'];

            if ($match['player1_time'] > 0 && $match['player2_time'] > 0) {
                if ($yourTime < $opponentTime) {
                    $winner = $username;
                } elseif ($opponentTime < $yourTime) {
                    $winner = ($match['player1'] === $username) ? $match['player2'] : $match['player1'];
                } else {
                    $winner = 'draw';
                }

                $stmt = $pdo->prepare("UPDATE matches SET status = 'completed' WHERE id = ?");
                $stmt->execute([$matchId]);

                echo json_encode([
                    'status' => 'completed',
                    'winner' => $winner,
                    'your_time' => floatval($yourTime),
                    'opponent_time' => floatval($opponentTime),
                    'you_won' => ($yourTime <= $opponentTime)
                ]);
            } else {
                echo json_encode(['status' => 'waiting']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Match not found']);
        }
    } elseif ($_POST['action'] === 'cancel_match') {
        $matchId = $_POST['match_id'];
        
        $stmt = $pdo->prepare("SELECT player1, player2, status FROM matches WHERE id = ?");
        $stmt->execute([$matchId]);
        $match = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($match && $match['status'] === 'waiting' && $match['player1'] === $username) {
            $stmt = $pdo->prepare("DELETE FROM matches WHERE id = ?");
            $stmt->execute([$matchId]);
            echo json_encode(['status' => 'cancelled']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Cannot cancel match']);
        }
    } else {
        throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}