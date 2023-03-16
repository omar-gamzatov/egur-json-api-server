<?php

declare(strict_types=1);

namespace Egur;

require_once("vendor/autoload.php");

use PDO;

mb_internal_encoding("UTF-8");

header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents('php://input'), true);

try {
    $pdo_link = new PDO('mysql:dbname=test;host=localhost', 'root', '');
} catch (PDOException) {
    die(json_encode(['error' => 'database connection failed']));
}

new RequestHandler($_GET['request'], $data, $pdo_link);
