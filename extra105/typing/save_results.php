<?php
/*------------------------------------------------
    セッションを開始
------------------------------------------------*/
session_start();
include('../db.php'); // PDO接続を含むファイルを読み込む

/*------------------------------------------------
    ユーザ情報を取得
------------------------------------------------*/
function getUserInfo()
{
    // セッションにユーザ名が保存されていない場合、ログインページにリダイレクト
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit();
    }
    // それ以外の場合、ユーザ名を取得
    else {
        return $_SESSION['username'];
    }
}

/*------------------------------------------------
    sqlを構築, 実行
------------------------------------------------*/
function saveResults($_username, $_data) {
    global $conn;
    // sql文を構築
    $sql = "INSERT INTO game_results (username, score, correct_chars, mistakes, accuracy, typing_speed, top_mistakes, ip_address, recorded_at, isDisplay) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    // 値をバインド
    $stmt->bind_param("s", $_username);
    bind_param("siiidisssi", $username, $_data[score], $score, $correct_chars, $mistakes, $accuracy, $typing_speed, $top_mistakes, $ip_address, $recorded_at, $is_display);
    // クエリの実行
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

/*------------------------------------------------
    処理の開始
------------------------------------------------*/
$username = $_SESSION['username'];
// POSTの生データを取得
$raw = file_get_contents('php://input');
$data = json_decode($raw);
if ()
?>