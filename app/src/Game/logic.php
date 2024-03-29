<?php

namespace App\Game;

require_once __DIR__ . '/board.php';
require_once __DIR__ . '/../Entity/database.php';

use Exception;

use App\Game\Board;
use App\Entity\Database;

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

    public function validMove(Player $player, $to, $from)
    {
        $tile = $this->move($player, $to, $from);
        if ($tile) {
            if ($this->board->isOccupied($from)) {
                $this->board->pushTile($from, $tile[1], $tile[0]);
            } else {
                $this->board->setTile($from, $tile[1], $tile[0]);
            }
        }
    }

    public function getValidPositionsPlay(Player $player)
    {
        $to = [];
        $offsets = $this->board->getOffsets();
        $positions = array_keys($this->board->getBoard());
        foreach ($offsets as $pq) {
            foreach ($positions as $pos) {
                $pq2 = explode(',', $pos);
                $res = ($pq[0] + $pq2[0]) . ',' . ($pq[1] + $pq2[1]);
                try {
                    $this->getValidPositions($player, $res);
                } catch (Exception $e) {
                    continue;
                }
                $to[] = $res;
            }
        }
        return array_unique($to);
    }

    public function getValidPositionsMove(Player $player)
    {
        $tiles = $this->board->getTiles($player);
        $pieces = $this->board->getPieces($player);
        $to = [];
        $offsets = $this->board->getOffsets();

        foreach ($pieces as $piece) {
            foreach ($tiles as $tile) {
                foreach ($offsets as $pq) {
                    $pq2 = explode(',', $tile);
                    $res = ($pq[0] + $pq2[0]) . ',' . ($pq[1] + $pq2[1]);
                    try {
                        $this->validMove($player, $res, $tile, $piece);
                        $to[] = $res;
                    } catch (Exception $e) {
                        continue;
                    }
                }
            }
        }
        return array_unique($to);
    }

    private function doesHiveSplit($to)
    {
        $board = $this->board;
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
        }
    }

    public function play(Player $player, $piece, $to)
    {
        $hand = $player->getHand();
        $this->getValidPositions($player, $to);
        if (!$hand->handHasPiece($piece)) {
            throw new Exception("Player does not have tile");
        } elseif ($hand->handSize() <= 8 && $hand->handHasTile("Q") && $piece != "Q") {
            throw new Exception("Must play queen bee");
        }
        return true;
    }

    public function move(Player $player, $to, $from)
    {
        $board = $this->board;
        $tile = null;
        try {
            if (!$board->isOccupied($from)) {
                throw new Exception("Board position is empty");
            } elseif (!$player->hasTile($from)) {
                throw new Exception("Tile is not owned by player");
            } elseif ($player->getHand()->handHasTile("Q")) {
                throw new Exception("Queen bee is not played");
            }
            $tile = $board->popTile($from);
            $player_pieces = array_keys($player->getHand()->getAvailablePieces());
            if (count($player_pieces) != 4 && !isset($player_pieces["Q"])) {
                $this->doesHiveSplit($to);
            }
            if ($from == $to) {
                throw new Exception("Tile must move");
            } elseif ($board->isOccupied($to) && $tile[1] != "B") {
                throw new Exception("Tile not empty");
            } elseif ($tile[1] == "Q" || $tile[1] == "B") {
                if (!$board->slide($from, $to)) {
                    throw new Exception("Tile must slide");
                }
            }
        } catch (Exception $e) {
            if ($tile) {
                if ($board->isOccupied($from)) {
                    $board->pushTile($from, $tile[1], $tile[0]);
                } else {
                    $board->setTile($from, $tile[1], $tile[0]);
                }
            }
            throw $e;
        }
        return $tile;
    }

    public function undo($last_move)
    {
        if (empty($this->board->getBoard())) {
            throw new Exception("No moves to undo");
        }

        $db_last_move = Database::getMove($last_move);
        Database::deleteMove($db_last_move['id']);

        Board::setState($db_last_move['state']);

        return $db_last_move['previous_id'];
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
