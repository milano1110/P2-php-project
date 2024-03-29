<?php

require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/../Entity/database.php';
require_once __DIR__ . '/board.php';
require_once __DIR__ . '/logic.php';
require_once __DIR__ . '/player.php';

use App\Game\Board;
use App\Entity\Database;
use App\Game\Logic;
use App\Game\Player;

session_start();

$dotenv = Dotenv\Dotenv::createImmutable("../");
$dotenv->load();

$gameId = $_SESSION['game_id'];
$lastMove = $_SESSION['last_move'] ?? null;
[$board, $players] = Logic::createGameFromSession($_SESSION);
$currentPlayer = $players[$_SESSION['player']];
$logic = new Logic($board);

if (!isset($_SESSION['last_move'])) {
    $_SESSION['last_move'] = null;
}

try {
    $logic->pass($currentPlayer);
    $insertId = Database::pass($gameId, $lastMove, Board::getState());
    $_SESSION['last_move'] = $insertId;
    $_SESSION['player'] = Player::getOpponent($currentPlayer);
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
}

header('Location: ../index.php');
exit();
