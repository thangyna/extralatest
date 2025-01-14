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

function getMistakes($_username) {
    global $conn;
    // sql文を構築
    $sql = "SELECT top_mistakes FROM game_results WHERE username = ?";
    $stmt = $conn->prepare($sql);
    // 値をバインド
    $stmt->bind_param("s", $_username);

    // クエリの実行
    $stmt->execute();
    $result = $stmt->get_result();

    // データを処理
    $mistakes_data = [];

    while ($row = $result->fetch_assoc()) {
        $top_mistakes = explode(',', $row['top_mistakes']);
        foreach ($top_mistakes as $mistake) {
            $mistake_parts = explode(':', $mistake);
            if (count($mistake_parts) == 2) {
                $key = $mistake_parts[0];
                $count = intval($mistake_parts[1]);
                if (!isset($mistakes_data[$key])) {
                    $mistakes_data[$key] = 0;
                }
                $mistakes_data[$key] += $count;
            }
        }
    }
    return $mistakes_data;
}

/*------------------------------------------------
    処理の開始
------------------------------------------------*/
$username = $_SESSION['username'];
$records = getRecord($username);
$mistakes = getMistakes($username);

$response = [
    'records' => $records,
    'mistakes'=> $mistakes
];

// 記録を返す
header('Content-Type: application/json');
echo json_encode($response);