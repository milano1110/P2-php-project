<?php

require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/../Entity/database.php';
require_once __DIR__ . '/hand.php';

use App\Entity\Database;
use App\Game\Hand;

session_start();

$dotenv = Dotenv\Dotenv::createImmutable("../");
$dotenv->load();

$_SESSION['board'] = [];
$_SESSION['hand'] = [Hand::$defaultHand, Hand::$defaultHand];
$_SESSION['player'] = 0;

$insertId = Database::restart();
$_SESSION['game_id'] = $insertId;

header('Location: ../index.php');
