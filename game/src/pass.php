<?php

session_start();

require_once 'vendor/autoload.php';

use Milano1110\Game\Board;
use Milano1110\Game\Database;

if (!isset($_SESSION['last_move'])) {
    $_SESSION['last_move'] = null;
}

$insertId = Database::pass($_SESSION['game_id'], $_SESSION['last_move'], Board::getState());
$_SESSION['last_move'] = $insertId;
$_SESSION['player'] = 1 - $_SESSION['player'];

header('Location: index.php');
exit();
