<?php

session_start();

require_once './vendor/autoload.php';

use Milano1110\Game\Board;
use Milano1110\Game\Database;

$piece = $_POST['piece'];
$to = $_POST['to'];

$player = $_SESSION['player'];
$board = new Board($_SESSION['board']);
$hand = $_SESSION['hand'][$player];

if (!$hand[$piece]) {
    $_SESSION['error'] = "Player does not have tile";
} elseif ($board->isOccupied($to)) {
    $_SESSION['error'] = 'Board position is not empty';
} elseif (!$board->isEmpty() && !$board->hasNeighbour($to)) {
    $_SESSION['error'] = "board position has no neighbour";
} elseif (array_sum($hand) < 11 && !$board->neighboursAreSameColor($player, $to)) {
    $_SESSION['error'] = "Board position has opposing neighbour";
} elseif (array_sum($hand) <= 8 && $hand['Q']) { {
        $_SESSION['error'] = 'Must play queen bee';
    }
} else {
    $_SESSION['board'][$to] = [[$_SESSION['player'], $piece]];
    $_SESSION['hand'][$player][$piece]--;
    $_SESSION['player'] = 1 - $_SESSION['player'];
    $_SESSION['last_move'] = isset($_SESSION['last_move']) ? $_SESSION['last_move'] : null;

    $insertId = Database::play($_SESSION['game_id'], $piece, $to, $_SESSION['last_move'], Board::getState());
    $_SESSION['last_move'] = $insertId;
}

header('Location: index.php');
exit();
