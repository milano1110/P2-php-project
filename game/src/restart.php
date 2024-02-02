<?php

session_start();

require_once './vendor/autoload.php';

use Milano1110\Game\Database;

$_SESSION['board'] = [];
$_SESSION['hand'] = [
    0 => ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3],
    1 => ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3]
];
$_SESSION['player'] = 0;

$insertId = Database::restart();
$_SESSION['game_id'] = $insertId;

header('Location: index.php');
