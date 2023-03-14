<?php
    require_once("RequestHandler.php");

    mb_internal_encoding("UTF-8");

    header("Content-Type: application/json; charset=utf-8");

    $data = json_decode(file_get_contents("php://input"), true);

    try {
        $pdo_link = new PDO('mysql:dbname=test;host=localhost', 'root', '');
    } catch (PDOException) {
        die(json_encode(['error' => 'database connection failed']));
    }
    
    $handler = new RequestHandler($_GET['request'], $data, $pdo_link);
    
   
?>
