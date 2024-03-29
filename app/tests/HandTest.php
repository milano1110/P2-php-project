<?php

require_once __DIR__ . '/../src/Game/Hand.php';

use PHPUnit\Framework\TestCase;
use App\Game\Hand;

class TestHand extends TestCase
{
    public function testHandHasPiece()
    {
        $hand = new Hand();
        $hand->removePiece('Q'); // Remove a piece to simulate that it was used

        $this->assertTrue($hand->handHasPiece('B'));
        $this->assertFalse($hand->handHasPiece('Q'));
    }

    public function testRemovePiece()
    {
        $hand = new Hand(["Q" => 1, "B" => 1, "S" => 2, "A" => 3, "G" => 3]);
        $hand->removePiece('B');

        $this->assertFalse($hand->handHasPiece('B'));
    }

    public function testHandSize()
    {
        $hand = new Hand();
        $hand->removePiece('Q'); // Remove a piece to simulate that it was used

        $this->assertEquals(10, $hand->handSize());
    }

    public function testGetAvailablePieces()
    {
        $hand = new Hand();
        $hand->removePiece('Q'); // Remove a piece to simulate that it was used

        $this->assertEquals(["B" => 2, "S" => 2, "A" => 3, "G" => 3], $hand->getAvailablePieces());
    }

    public function testHandHasTile()
    {
        $hand = new Hand();

        $this->assertTrue($hand->handHasTile('Q'));
        $this->assertFalse($hand->handHasTile('Z'));
    }
}
