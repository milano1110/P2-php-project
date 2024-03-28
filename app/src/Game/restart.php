<?php

require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/../Entity/database.php';

use App\Entity\Database;

session_start();

$dotenv = Dotenv\Dotenv::createImmutable("../");
$dotenv->load();

$_SESSION['board'] = [];
$_SESSION['hand'] = [
    0 => ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3],
    1 => ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3]
];
$_SESSION['player'] = 0;

$insertId = Database::restart();
$_SESSION['game_id'] = $insertId;

header('Location: ../index.php');
