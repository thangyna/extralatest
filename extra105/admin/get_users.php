<?php
// filepath: c:\Users\er-GOD-ic\Documents\Projects\github\extralatest\extra105\admin\get_users.php
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
    ユーザーデータを取得
------------------------------------------------*/
function getUsers()
{
    global $pdo;
    $stmt = $pdo->prepare('SELECT id, username FROM users');
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/*------------------------------------------------
    処理の開始
------------------------------------------------*/
header('Content-Type: application/json');
$username = getUserInfo();

try {
    $users = getUsers();
    echo json_encode(['status' => 'success', 'users' => $users]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>