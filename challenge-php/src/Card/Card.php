<?php

namespace Poker\Card;

class Card
{
    public string $value;
    public string $suit;
    public string $face;
    public int $pointValue;

    public function __construct(string $card)
    {
        // Validate passed card string
        if (!$this->isValid($card)) {
            throw new \Exception("ERROR: The provided string, \"$card\", is not a valid Card value.");
        }

        // Populate Card properties
        $this->value = $card;
        $this->extractSuitAndFace();
        $this->setPointValue();
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getSuit(): string
    {
        return $this->suit;
    }

    public function getFace(): string
    {
        return $this->face;
    }

    public function getPointValue(): int
    {
        return $this->pointValue;
    }

    private function isValid(string $card): bool
    {
        $cardPattern = "/^[AKQJ1-9]0?[hdsc]$/";
        return preg_match($cardPattern, $card) === 1;
    }

    private function extractSuitAndFace(): void
    {
        $this->suit = substr($this->value, -1);
        $this->face = preg_replace("/" . $this->suit . "/", '', $this->value);
    }

    private function setPointValue(): void
    {
        $cardValuesArray = require 'CardValuesArray.php';
        $this->value = $cardValuesArray[$this->face];
    }
}
