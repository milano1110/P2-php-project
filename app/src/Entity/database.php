<?php

namespace App\Entity;

use mysqli;
// use Dotenv\Dotenv;

class Database
{
    private static $connection = null;

    // private function __construct()
    // {
    //     $dotenv = Dotenv::createImmutable("var/www/html/");
    //     $dotenv->load();
    // }

    private static function connect()
    {
        if (self::$connection === null) {
            $host = $_ENV['DB_HOST'];
            $username = $_ENV['DB_USER'];
            $password = $_ENV['DB_PASSWORD'];
            $database = $_ENV['DB_NAME'];

            self::$connection = new mysqli($host, $username, $password, $database);

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

    public static function getMoves($gameId)
    {
        $db = Database::connect();
        $stmt = $db->prepare('SELECT * FROM moves WHERE game_id = ' . $gameId);
        $stmt->execute();
        return $stmt->get_result();
    }
}
