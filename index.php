<?php

declare(strict_types=1);

namespace Egur;

require_once("vendor/autoload.php");

use PDO;
use Egur\Functions\Messages;

mb_internal_encoding("UTF-8");

header('Content-Type: application/json; charset=utf-8');
header('API-Version: 1.0');

$data = json_decode(file_get_contents('php://input'), true);

try {
    $pdo_link = new PDO('mysql:dbname=test;host=localhost', 'root', '');
} catch (PDOException) {
    Messages::dieWithError('database connection failed');
}
if ($_GET['request']) {
    new RequestHandler($_GET['request'], $data, $pdo_link);
} else {
    Messages::dieWithError('unknown request');
}
