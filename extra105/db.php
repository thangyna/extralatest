<?php
$host = 'localhost';
if ($_SERVER['HTTP_HOST'] == 'localhost') {
    $username = 'root';
    $password = 'obrien'; // or your password
    $dbname = 'cf760008_typing';
}
elseif ($_SERVER['HTTP_HOST'] == 'localhost:8080') {
    $username = 'root';
    $password = 'makoto66'; // or your password
    $dbname = 'cf760008_typing';
}
else {
    $username = 'cf760008_aioi';
    $password = 'makoto66';
    $dbname = 'cf760008_typing';
}

$conn = new mysqli($host, $username, $password, $dbname);

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Could not connect to the database $dbname :" . $e->getMessage());
}
?>