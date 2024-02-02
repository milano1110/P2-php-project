<?php

namespace Milano1110\Game;

use mysqli;

class Database
{
    private static $connection = null;

    private static function connect()
    {
        if (self::$connection === null) {
            $host = getenv('HOST') ?: 'database';
            $user = getenv('USER') ?: 'hiveuser';
            $password = getenv('PASSWORD') ?: 'hivepassword';
            $dbName = getenv('NAME') ?: 'hive';

            self::$connection = new mysqli($host, $user, $password, $dbName);

            if (self::$connection->connect_error) {
                die("Connection failed: " . self::$connection->connect_error);
            }
        }
        return self::$connection;
    }

    public static function restart()
    {
        $db = Database::connect();
        $db->prepare('INSERT INTO games VALUES ()')->execute();
        return $db->insert_id;
    }

    public static function move($gameId, $from, $to, $lastMove, $state)
    {
        $db = self::connect();
        $stmt = $db->prepare('INSERT INTO moves (game_id, type, move_from, move_to, previous_id, state)
        VALUES (?, "move", ?, ?, ?, ?)');

        $stmt->bind_param('issis', $gameId, $from, $to, $lastMove, $state);
        $stmt->execute();
        return $db->insert_id;
    }

    public static function pass($gameId, $lastMove, $state)
    {
        $db = self::connect();
        $stmt = $db->prepare('INSERT INTO moves (game_id, type, move_from, move_to, previous_id, state)
        VALUES (?, "pass", NULL, NULL, ?, ?)');

        $stmt->bind_param('iis', $gameId, $lastMove, $state);
        $stmt->execute();
        return $db->insert_id;
    }

    public static function play($gameId, $piece, $to, $lastMove, $state)
    {
        $db = self::connect();
        $stmt = $db->prepare('INSERT INTO moves (game_id, type, move_from, move_to, previous_id, state)
        VALUES (?, "play", ?, ?, ?, ?)');

        $stmt->bind_param('issis', $gameId, $piece, $to, $lastMove, $state);
        $stmt->execute();
        return $db->insert_id;
    }

    public static function getMoves()
    {
        $db = Database::connect();
        $stmt = $db->prepare('SELECT * FROM moves WHERE game_id = ' . $_SESSION['game_id']);
        $stmt->execute();
        return $stmt->get_result();
    }
}
