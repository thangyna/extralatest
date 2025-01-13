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
function getRecord($_username) {
    global $conn;
    // sql文を構築
    $sql = "SELECT DATE(recorded_at) as date, score, correct_chars, mistakes, accuracy
            FROM game_results 
            WHERE username = ? 
            ORDER BY recorded_at ASC";
    $stmt = $conn->prepare($sql);
    // 値をバインド
    $stmt->bind_param("s", $_username);

    // クエリの実行
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

/*------------------------------------------------
    処理の開始
------------------------------------------------*/
$username = $_SESSION['username'];
$records = getRecord($username);

// 記録を返す
header('Content-Type: application/json');
echo json_encode($records);