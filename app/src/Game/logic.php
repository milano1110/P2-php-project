<?php

namespace App\Game;

require_once __DIR__ . '/board.php';

use Exception;

use App\Game\Board;

class Logic
{
    public Board $board;

    public function __construct(Board $board)
    {
        $this->board = $board;
    }

    public function getValidPositions(Player $player, $to)
    {
        $hand = $player->getHand();
        if ($this->board->isOccupied($to)) {
            throw new Exception("Position is already occupied");
        } elseif (!$this->board->isEmpty() && !$this->board->hasNeighbour($to)) {
            throw new Exception("Position has no neighbour");
        } elseif ($hand->handSize() < 11 && !$this->board->neighboursAreSameColor($player->getPlayer(), $to)) {
            throw new Exception("Position has opposing neighbour");
        }
    }

    public function play(Player $player, $piece, $to)
    {
        $hand = $player->getHand();
        $this->getValidPositions($player, $to);
        if (!$hand->handHasPiece($piece)) {
            throw new Exception("Player does not have tile");
        } elseif ($hand->handSize() <= 8 && $hand->handHasTile("Q")) {
            throw new Exception("Must play queen bee");
        }
    }

    public function move(Player $player, $to, $from, $board)
    {
        $tile = null;
        try {
            if (!$board->isOccupied($from)) {
                throw new Exception("Board position is empty");
            } elseif (!$player->hasTile($from)) {
                throw new Exception("Tile is not owned by player");
            } elseif ($player->getHand()->handHasTile("Q")) {
                throw new Exception("Queen bee is not played");
            } else {
                $tile = $board->popTile($from);
                if (!$board->hasNeighbour($to)) {
                    throw new Exception("Move would split hive");
                }
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
                    throw new Exception("Move would split hive");
                } else {
                    if ($from == $to) {
                        throw new Exception("Tile must move");
                    } elseif ($board->isOccupied($to) && $tile[1] != "B") {
                        throw new Exception("Tile not empty");
                    } elseif ($tile[1] == "Q" || $tile[1] == "B") {
                        if (!$board->slide($from, $to)) {
                            throw new Exception("Tile must slide");
                        }
                    }
                }
            }
        } catch (Exception $e) {
            if ($tile) {
                if ($board->isOccupied($from)) {
                    $board->pushTile($from, $tile[1], $tile[0]);
                } else {
                    $board[$from] = [$tile];
                }
            }
            throw $e;
        }
        return $tile;
    }

    public static function createGameFromSession(array $session): array
    {
        $board = new Board($session['board']);
        if (isset($session['hand'])) {
            $hands = [new Hand($session['hand'][0]), new Hand($session['hand'][1])];
        } else {
            $hands = [new Hand(), new Hand()];
        }
        $players = [new Player(0, $hands[0], $board), new Player(1, $hands[1], $board)];

        return [$board, $players];
    }
}