<?php

require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/../Game/logic.php';

use App\Game\Logic;

session_start();

$dotenv = Dotenv\Dotenv::createImmutable("../");
$dotenv->load();

$last_move = $_SESSION['last_move'];
[$board, $players] = Logic::createGameFromSession($_SESSION);

try {
    $logic = new Logic($board);
    $previousId = $logic->undo($last_move);
    $_SESSION['last_move'] = $previousId;
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
}

header('Location: ../index.php');
