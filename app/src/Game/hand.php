<?php

namespace App\Game;

class Hand
{
    private array $hand;

    public static array $defaultHand =
    ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3];

    public function __construct(array $hand = null)
    {
        if ($hand == null) {
            $this->hand = self::$defaultHand;
        } else {
            $this->hand = $hand;
        }
    }

    public function getHandArray(): array
    {
        return $this->hand;
    }

    public function removePiece(string $piece): void
    {
        $this->hand[$piece]--;
    }

    public function handHasPiece(string $piece): bool
    {
        return $this->hand[$piece] > 0;
    }

    public function handSize(): int
    {
        return array_sum($this->hand);
    }

    public function getAvailablePieces()
    {
        return array_filter($this->hand);
    }

    public function handHasTile(string $tile): bool
    {
        return array_key_exists($tile, $this->hand);
    }
}
