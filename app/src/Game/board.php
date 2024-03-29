<?php

namespace App\Game;

require_once __DIR__ . '/../Entity/database.php';
require_once __DIR__ . '/hand.php';
require_once __DIR__ . '/player.php';

use App\Entity\Database;
use App\Game\Hand;
use App\Game\Player;

class Board
{
    private static $offsets = [[0, 1], [0, -1], [1, 0], [-1, 0], [-1, 1], [1, -1]];
    public array $board;

    public function __construct(array $board)
    {
        $this->board = $board;
    }

    public static function getOffsets()
    {
        return self::$offsets;
    }

    public function getBoard()
    {
        return $this->board;
    }

    public static function getState()
    {
        return serialize([$_SESSION['hand'], $_SESSION['board'], $_SESSION['player']]);
    }

    public static function setState($state)
    {
        list($_SESSION['hand'], $_SESSION['board'], $_SESSION['player']) = unserialize($state);
    }

    public function isEmpty()
    {
        return count($this->board) === 0;
    }

    public function getMoves()
    {
        return Database::getMoves($_SESSION['game_id']);
    }

    public function getKeys()
    {
        return array_keys($this->board);
    }

    public function getNonEmptyTiles()
    {
        return array_filter($this->board, function ($tileStack) {
            return !empty($tileStack);
        });
    }

    public function isPlayerOccupying($from, $player)
    {
        if (isset($this->board[$from]) && count($this->board[$from]) > 0) {
            return $this->board[$from][count($this->board[$from]) - 1][0] == $player;
        }
        return false;
    }

    public function isOccupied(string $position): bool
    {
        return isset($this->board[$position]);
    }

    public function isNeighbour($a, $b)
    {
        $a = explode(',', $a);
        $b = explode(',', $b);

        if (
            $a[0] == $b[0] && abs($a[1] - $b[1]) == 1 ||
            $a[1] == $b[1] && abs($a[0] - $b[0]) == 1 ||
            $a[0] + $a[1] == $b[0] + $b[1]
        ) {
            return true;
        }
    }

    public function hasNeighbour($a)
    {
        foreach (array_keys($this->board) as $b) {
            if ($this->isNeighbour($a, $b)) {
                return true;
            }
        }
        return false;
    }

    public function neighboursAreSameColor($player, $a)
    {
        foreach ($this->board as $b => $st) {
            if (!$st) {
                continue;
            }
            $c = $st[count($st) - 1][0];
            if ($c != $player && $this->isNeighbour($a, $b)) {
                return false;
            }
        }
        return true;
    }

    public function getTiles(Player $player)
    {
        $tiles = array_filter($this->board, function ($tileStack) use ($player) {
            return is_array($tileStack) && isset($tileStack[0]) && is_array($tileStack[0]) && $tileStack[0][0] == $player->getPlayer();
        });
        return array_keys($tiles);
    }

    public function getPieces(Player $player)
    {
        $tiles = $this->getTiles($player);
        $pieces = [];
        foreach ($tiles as $tile) {
            $topTile = end($this->board[$tile]);
            $piece = $topTile[1];
            $pieces[] = $piece;
        }
        return $pieces;
    }

    public function popTile(string $position): array
    {
        return array_pop($this->board[$position]);
    }

    public function pushTile(string $position, string $piece, int $player)
    {
        array_push($this->board[$position], array($player, $piece));
    }

    public function setTile(string $position, string $tile, int $player)
    {
        $this->board[$position] = [[$player, $tile]];
    }

    public function len($tile)
    {
        return $tile ? count($tile) : 0;
    }

    public function slide($from, $to)
    {
        if (!$this->hasNeighbour($to) || !$this->isNeighbour($from, $to)) {
            return false;
        }

        $b = explode(',', $to);
        $common = [];
        foreach (self::$offsets as $pq) {
            $p = $b[0] + $pq[0];
            $q = $b[1] + $pq[1];
            if ($this->isNeighbour($from, $p . "," . $q)) {
                $common[] = $p . "," . $q;
            }
        }

        if (
            !$this->board[$common[0]] &&
            !$this->board[$common[1]] &&
            !$this->board[$from] &&
            !$this->board[$to]
        ) {
            return false;
        }

        return min(count($this->board[$common[0]]), count($this->board[$common[1]]))
            <= max(count($this->board[$from]), count($this->board[$to]));
    }
}
