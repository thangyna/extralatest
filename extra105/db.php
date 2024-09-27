<?php
$servername = "localhost";
$username = "cf760008_aioi";
$password = "makoto66";
$dbname = "cf760008_typing";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>