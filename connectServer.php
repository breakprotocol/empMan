<?php
$servername = "localhost";
$username = "bhavin-gala";
$password = "dholu&ariha@12345";
$db = "boxxit";
// Create connection
$mysqli = new mysqli($servername, $username, $password,$db);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

?>