<?php

require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/../Entity/database.php';
require_once __DIR__ . '/board.php';

use App\Game\Board;
use App\Entity\Database;

session_start();

$dotenv = Dotenv\Dotenv::createImmutable("../");
$dotenv->load();

if (!isset($_SESSION['last_move'])) {
    $_SESSION['last_move'] = null;
}

$insertId = Database::pass($_SESSION['game_id'], $_SESSION['last_move'], Board::getState());
$_SESSION['last_move'] = $insertId;
$_SESSION['player'] = 1 - $_SESSION['player'];

header('Location: ../index.php');
exit();
