<?php

//  require('function.php');

// $db = 'mysql://b035f5d09447ce:e95bdbe4@us-cdbr-east-02.cleardb.com/heroku_ca219877e2780ce?reconnect=true';
$db = 'mysql:host=us-cdbr-east-02.cleardb.com;dbname=heroku_ca219877e2780ce;charset=utf8';
$user ='b035f5d09447ce';
$password = 'e95bdbe4';

try {
    $pdo = new PDO($db, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    exit;
}
