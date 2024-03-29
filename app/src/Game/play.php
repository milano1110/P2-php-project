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

$piece = $_POST['piece'];
$to = $_POST['to'];

$gameId = $_SESSION['game_id'];
$hands = $_SESSION['hand'];
$lastMove = $_SESSION['last_move'] ?? null;

[$board, $players] = Logic::createGameFromSession($_SESSION);
$currentPlayer = $players[$_SESSION['player']];

try {
    $logic = new Logic($board);
    $logic->play($currentPlayer, $piece, $to);

    $board->setTile($to, $piece, $currentPlayer->getPlayer());

    $currentPlayer->getHand()->removePiece($piece);

    $insertId = Database::play($gameId, $piece, $to, $lastMove, Board::getState());

    $_SESSION['player'] = Player::getOpponent($currentPlayer);
    $_SESSION['board'] = $board->getBoard();
    $_SESSION['hand'] = [$players[0]->getHand()->getHandArray(), $players[1]->getHand()->getHandArray()];
    $_SESSION['last_move'] = $insertId;
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
}

header('Location: ../index.php');
