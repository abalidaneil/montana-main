<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// session_start();

// $host = "sql205.infinityfree.com"; $user = "if0_41855521"; $pass = "Gocd1cQ1daVSf"; $dbname = "if0_41578937_real";

$host = 'localhost';
$dbname = 'montana';
$username = 'root';
$password = 'usbw';

function getDatabaseConnection($host, $dbname, $username, $password) {
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch(PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

$pdo = getDatabaseConnection($host, $dbname, $username, $password);

// Optionally, you can close the connection
// $pdo = null;

?>
