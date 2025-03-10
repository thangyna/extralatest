<?php
/*------------------------------------------------
    セッションを開始
------------------------------------------------*/
session_start();
include('../db.php');

/*------------------------------------------------
    ユーザ情報を取得
------------------------------------------------*/
function getUserInfo()
{
    // セッションにユーザ名が保存されていない場合、ログインページにリダイレクト
    if (!isset($_SESSION['username'])) {
        header("Location: ../login.php");
        exit();
    }
    // それ以外の場合、ユーザ名を取得
    else {
        return $_SESSION['username'];
    }
}

/*------------------------------------------------
    フィードバックデータを取得
------------------------------------------------*/
function getFeedback()
{
    global $pdo;
    $stmt = $pdo->prepare('SELECT username, message, created_at FROM Inquiry ORDER BY created_at DESC');
    if (!$stmt) {
        throw new Exception('クエリの準備に失敗しました: ' . $pdo->errorInfo()[2]);
    }
    if (!$stmt->execute()) {
        throw new Exception('クエリの実行に失敗しました: ' . $stmt->errorInfo()[2]);
    }
    return $stmt->fetchAll(PDO::FETCH_ASSOC);


}

/*------------------------------------------------
    処理の開始
------------------------------------------------*/
header('Content-Type: application/json');
$username = getUserInfo();

try {
    $feedback = getFeedback();
    echo json_encode(['status' => 'success', 'feedback' => $feedback]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>