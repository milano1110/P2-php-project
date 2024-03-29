<?php

use PHPUnit\Framework\TestCase;
use App\Game\Player;
use App\Game\Hand;
use App\Game\Board;

class TestPlayer extends TestCase
{
    public function testGetPlayer()
    {
        $hand = new Hand();
        $board = new Board([]);
        $player = new Player(0, $hand, $board);

        $this->assertEquals(0, $player->getPlayer());
    }

    public function testGetHand()
    {
        $hand = new Hand();
        $board = new Board([]);
        $player = new Player(0, $hand, $board);

        $this->assertEquals($hand, $player->getHand());
    }

    public function testGetPlayerTilesSingle()
    {
        $hand = new Hand(["Q" => 0, "B" => 2, "S" => 2, "A" => 3, "G" => 3]);
        $board = new Board(['0,0' => [[0, 'Q']], '0,1' => [[1, 'B']]]);
        $player = new Player(0, $hand, $board);

        $this->assertEquals(['0,0'], $player->getPlayerTiles());
    }

    public function testGetPlayerTilesMultiple()
    {
        $hand = new Hand(["Q" => 0, "B" => 1, "S" => 2, "A" => 3, "G" => 3]);
        $board = new Board(['0,0' => [[0, 'Q']], '0,1' => [[1, 'B']], '1,0' => [[0, 'B']]]);
        $player = new Player(0, $hand, $board);

        $this->assertEquals(['0,0', '1,0'], $player->getPlayerTiles());
    }

    public function testGetValidPositionsEmptyBoard()
    {
        $hand = new Hand();
        $board = new Board([]);
        $player = new Player(0, $hand, $board);
        $this->assertEquals([], $player->getValidPositions());
    }

    public function testGetValidPositions()
    {
        $hand = new Hand(["Q" => 0, "B" => 2, "S" => 2, "A" => 3, "G" => 3]);
        $board = new Board([
            '0,0' => [[0, 'Q']],
            '1,0' => [[1, 'Q']],
        ]);
        $player = new Player(0, $hand, $board);

        $this->assertEquals(['0,-1', '-1,0', '-1,1'], $player->getValidPositions());
    }

    public function testGetOpponent()
    {
        $hand = new Hand();
        $board = new Board([]);
        $player1 = new Player(1, $hand, $board);
        $player2 = new Player(0, $hand, $board);

        $this->assertEquals($player2->getPlayer(), Player::getOpponent($player1));
        $this->assertEquals($player1->getPlayer(), Player::getOpponent($player2));
    }
}
