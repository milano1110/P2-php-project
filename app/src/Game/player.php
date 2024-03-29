<?php

namespace App\Game;

require_once __DIR__ . '/logic.php';
require_once __DIR__ . '/board.php';
require_once __DIR__ . '/hand.php';

use Exception;

use App\Game\Logic;
use App\Game\Board;
use App\Game\Hand;

class Player
{
    private int $player;
    private Hand $hand;
    private Board $board;

    public function __construct(int $player, Hand $hand, Board $board)
    {
        $this->player = $player;
        $this->hand = $hand;
        $this->board = $board;
    }

    public function getPlayer(): int
    {
        return $this->player;
    }

    public function getHand(): Hand
    {
        return $this->hand;
    }

    public function hasTile(string $place)
    {
        return $this->board->isPlayerOccupying($place, $this->player);
    }

    public function getPlayerTiles()
    {
        $playerId = Player::getPlayer();

        $playerTiles = array_filter(
            $this->board->getBoard(),
            function ($tile) use ($playerId) {
                return is_array($tile) && isset($tile[0]) && is_array($tile[0]) && $tile[0][0] === $playerId;
            }
        );
        return array_keys($playerTiles);
    }

    public function getValidPositions()
    {
        $board = $this->board;
        $logic = new Logic($board);
        $to = [];
        foreach ($board::getOffsets() as $pq) {
            foreach ($board->getKeys() as $pos) {
                $pq2 = explode(',', $pos);
                try {
                    $logic->getValidPositions($this, ($pq[0] + $pq2[0]) . ',' . ($pq[1] + $pq2[1]));
                } catch (Exception $e) {
                    continue;
                }
                $to[] = ($pq[0] + $pq2[0]) . ',' . ($pq[1] + $pq2[1]);
            }
        }
        return array_unique($to);
    }

    public static function getOpponent(Player $currentPlayer)
    {
        return 1 - $currentPlayer->getPlayer();
    }
}
