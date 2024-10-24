<?php
$host = 'localhost'; 
$db   = 'u571101154_todo'; 
$user = 'u571101154_todo'; 
$pass = 'LabFinal123'; 
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

