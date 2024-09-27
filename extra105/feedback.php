<?php
session_start();
include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_SESSION['username'];
    $message = $_POST['message'];

    $stmt = $conn->prepare("INSERT INTO Inquiry (username, message) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $message);
    $stmt->execute();
    $stmt->close();

    header("Location: game.php"); // Redirect back to the game page
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>ご意見箱</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <h2>ご意見をお聞かせください</h2>
    <form action="feedback.php" method="post">
        <textarea name="message" required></textarea>
        <input type="submit" value="送信">
    </form>
</body>
</html>