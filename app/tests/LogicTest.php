<?php

require_once __DIR__ . '/../src/Game/Logic.php';
require_once __DIR__ . '/../src/Game/Board.php';
require_once __DIR__ . '/../src/Game/Hand.php';
require_once __DIR__ . '/../src/Game/Player.php';
// require_once __DIR__ . '/../src/Entity/Database.php';

use PHPUnit\Framework\TestCase;
use App\Game\Logic;
use App\Game\Board;
use App\Entity\Database;
use App\Game\Hand;
use App\Game\Player;

class LogicTest extends TestCase
{

    public function testUndo()
    {
        // Mock the Database class
        $databaseMock = Mockery::mock('overload:App\Entity\Database');
        $databaseMock->shouldReceive('getMove')
            ->with('A1')
            ->andReturn(['id' => 1, 'state' => 'state1', 'previous_id' => 'A0']);
        $databaseMock->shouldReceive('deleteMove')
            ->with(1)
            ->andReturn(true);

        // Mock the Board class
        $boardMock = Mockery::mock('overload:App\Game\Board');
        $boardMock->shouldReceive('getBoard')
            ->andReturn(['A1' => 'X']);
        $boardMock->shouldReceive('setState')
            ->with('state1');

        $logic = new Logic($boardMock);
        $previous_id = $logic->undo('A1');

        $this->assertEquals('A0', $previous_id);
    }

    public function tearDown(): void
    {
        Mockery::close();
    }

    public function testPlayQueenMoveFour()
    {
        $board = new Board([
            '0,0' => [[0, 'S']],
            '0,1' => [[1, 'S']],
            '0,-1' => [[0, 'A']],
            '0,2' => [[1, 'A']],
            '0,-2' => [[0, 'B']],
            '0,3' => [[1, 'B']]
        ]);
        $hand = new Hand(["Q" => 1, "B" => 1, "S" => 1, "A" => 2, "G" => 3]);
        $logic = new Logic($board);
        $player = new Player(0, $hand, $board);
        $this->assertTrue($logic->play($player, 'Q', '0,-3'));
    }

    public function testNoQueenMoveFour()
    {
        $board = new Board([
            '0,0' => [[0, 'S']],
            '0,1' => [[1, 'S']],
            '0,-1' => [[0, 'A']],
            '0,2' => [[1, 'A']],
            '0,-2' => [[0, 'B']],
            '0,3' => [[1, 'B']]
        ]);
        $hand = new Hand(["Q" => 1, "B" => 1, "S" => 1, "A" => 2, "G" => 3]);
        $logic = new Logic($board);
        $player = new Player(0, $hand, $board);
        $this->expectExceptionMessage('Must play queen bee');
        $logic->play($player, 'B', '0,-3');
    }

    public function testPass()
    {
        $board = new Board([
            '0,0' => [[0, 'Q'], '0,1' => [1, 'Q']]
        ]);
        $hand = new Hand(["Q" => 0, "B" => 0, "S" => 0, "A" => 0, "G" => 0]);
        $player = new Player(0, $hand, $board);
        $logic = new Logic($board);
        $this->assertTrue($logic->pass($player));
    }

    public function testPassException()
    {
        $board = new Board([
            "0,0" => [[0, "Q"]]
        ]);
        $hand = new Hand();
        $player = new Player(1, $hand, $board);
        $logic = new Logic($board);
        $this->expectException(Exception::class);
        $logic->pass($player);
    }
}
