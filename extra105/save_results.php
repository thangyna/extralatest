<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include('db.php');

// デバッグ情報
file_put_contents('debug.log', print_r($_POST, true) . "\n", FILE_APPEND);

if (!isset($_SESSION['username'])) {
    echo "ログインしてください";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_SESSION['username'];
    $score = intval($_POST['score']);
    $correct_chars = intval($_POST['correct_chars']);
    $mistakes = intval($_POST['mistakes']);
    $accuracy = floatval($_POST['accuracy']);
    $typing_speed = intval($_POST['typing_speed']);
    $top_mistakes = $_POST['top_mistakes'];
    $is_display = $_POST['is_display'] ? 1 : 0;
    
    // IPアドレスの取得
    $ip_address = $_SERVER['REMOTE_ADDR'];
    
    // デバッグ: IPアドレスをログに記録
    file_put_contents('debug.log', "Original IP: " . $ip_address . "\n", FILE_APPEND);

    $datetime = new DateTime('now', new DateTimeZone('Asia/Tokyo'));
    $recorded_at = $datetime->format('Y-m-d H:i:s');

    $sql = "INSERT INTO game_results (username, score, correct_chars, mistakes, accuracy, typing_speed, top_mistakes, ip_address, recorded_at, isDisplay)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siiidisssi", $username, $score, $correct_chars, $mistakes, $accuracy, $typing_speed, $top_mistakes, $ip_address, $recorded_at, $is_display);

    if ($stmt->execute()) {
        echo "結果が保存されました。";
        
        // デバッグ: 保存されたデータを確認
        $check_sql = "SELECT * FROM game_results WHERE username = ? ORDER BY recorded_at DESC LIMIT 1";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $username);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        $row = $result->fetch_assoc();
        file_put_contents('debug.log', "Saved IP: " . $row['ip_address'] . "\n", FILE_APPEND);
        $check_stmt->close();
    } else {
        echo "エラーが発生しました: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>