<?php

session_start();

require_once 'vendor/autoload.php';

use Milano1110\Game\Board;
use Milano1110\Game\Database;

$from = $_POST['from'];
$to = $_POST['to'];

$player = $_SESSION['player'];
$board = new Board($_SESSION['board']);
$hand = $_SESSION['hand'][$player];
unset($_SESSION['error']);

if (!$board->isOccupied($from)) {
    $_SESSION['error'] = 'Board position is empty';
} elseif (!$board->isPlayerOccupying($from, $player)) {
    $_SESSION['error'] = "Tile is not owned by player";
} elseif ($hand['Q']) {
    $_SESSION['error'] = "Queen bee is not played";
} else {
    $tile = $board->popTile($from);
    if (!$board->hasNeighbour($to)) {
        $_SESSION['error'] = "Move would split hive";
    } else {
        $all = $board->getKeys();
        $queue = [array_shift($all)];
        while ($queue) {
            $next = explode(',', array_shift($queue));
            foreach (Board::getOffsets() as $pq) {
                list($p, $q) = $pq;
                $p += $next[0];
                $q += $next[1];
                if (in_array("$p,$q", $all)) {
                    $queue[] = "$p,$q";
                    $all = array_diff($all, ["$p,$q"]);
                }
            }
        }
        if ($all) {
            $_SESSION['error'] = "Move would split hive";
        } else {
            if ($from == $to) {
                $_SESSION['error'] = 'Tile must move';
            } elseif ($board->isOccupied($to) && $tile[1] != "B") {
                $_SESSION['error'] = 'Tile not empty';
            } elseif ($tile[1] == "Q" || $tile[1] == "B") {
                if (!$board->slide($from, $to)) {
                    $_SESSION['error'] = 'Tile must slide';
                }
            }
        }
    }
    if (isset($_SESSION['error'])) {
        if ($board->isOccupied($from)) {
            $board->pushTile($from, $tile[1], $tile[0]);
        } else {
            $board[$from] = [$tile];
        }
    } else {
        if ($board->isOccupied($to)) {
            $board->pushTile($to, $tile[0], $tile[1]);
        } else {
            $board[$to] = [$tile];
        }
        $_SESSION['player'] = 1 - $_SESSION['player'];
        $insertId = Database::move($_SESSION['game_id'], $piece, $to, $_SESSION['last_move'], Board::getState());
        $_SESSION['last_move'] = $insertId;
    }
    $_SESSION['board'] = $board->getBoard();
}

header('Location: index.php');
exit();
