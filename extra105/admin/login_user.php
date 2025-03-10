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
    ユーザをログイン
------------------------------------------------*/
function loginUser($userId)
{
    global $pdo;
    $stmt = $pdo->prepare('SELECT username FROM users WHERE id = ?');
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        $_SESSION['username'] = $user['username'];
        return ['status' => 'success', 'message' => 'ログインに成功しました。', 'redirect' => '../typing/typing_game.html'];
    } else {
        return ['status' => 'error', 'message' => 'ユーザーが見つかりません。'];
    }
}

/*------------------------------------------------
    処理の開始
------------------------------------------------*/
header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['userId'])) {
        throw new Exception('ユーザーIDが指定されていません。');
    }
    $userId = $data['userId'];
    $response = loginUser($userId);
    echo json_encode($response);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>