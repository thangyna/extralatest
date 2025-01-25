<?php
/*------------------------------------------------
    セッションを開始
------------------------------------------------*/
session_start();
require_once('../db.php');  // PDO接続を含むファイルを読み込む

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
    ランキングを取得
------------------------------------------------*/
function getRanking() {
    global $pdo;
    // 今日の日付を取得 (日本時間)
    date_default_timezone_set('Asia/Tokyo');
    $today = date('Y-m-d');

    // sql文を構築
    $sql = "
        SELECT username, MAX(score) AS score
        FROM game_results
        WHERE DATE(recorded_at) = :today AND isDisplay = 1
        GROUP BY username
        ORDER BY score DESC
        LIMIT 5
    ";
    $stmt = $pdo->prepare($sql);

    // クエリの実行
    $stmt->execute(['today' => $today]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/*------------------------------------------------
    処理の開始
------------------------------------------------*/
$username = getUserInfo();
$ranking = getRanking();

$response = [
    'username' => $username,
    'ranking' => $ranking
];

header('Content-Type: application/json');
echo json_encode($response);
?>