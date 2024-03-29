<?php

require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/../Entity/database.php';
require_once __DIR__ . '/board.php';
require_once __DIR__ . '/logic.php';
require_once __DIR__ . '/hand.php';
require_once __DIR__ . '/player.php';

use App\Game\Board;
use App\Entity\Database;
use App\Game\Logic;
use App\Game\Player;

session_start();

$dotenv = Dotenv\Dotenv::createImmutable("../");
$dotenv->load();

$from = $_POST['from'];
$to = $_POST['to'];

$gameId = $_SESSION['game_id'];
$hands = $_SESSION['hand'];
$lastMove = $_SESSION['last_move'];

[$board, $players] = Logic::createGameFromSession($_SESSION);
$currentPlayer = $players[$_SESSION['player']];

unset($_SESSION['error']);

try {
    $logic = new Logic($board);
    $tile = $logic->move($currentPlayer, $to, $from, $board);

    if ($board->isOccupied($to)) {
        $board->pushTile($to, $tile[1], $tile[0]);
    } else {
        $board->setTile($to, $tile[1], $tile[0]);
    }
    $insertId = Database::move($gameId, $from, $to, $lastMove, Board::getState());

    $_SESSION['last_move'] = $insertId;
    $_SESSION['player'] = Player::getOpponent($currentPlayer);

    $_SESSION['board'] = $board->getBoard();
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
}

header('Location: ../index.php');
