<?php

namespace Poker\PokerHand;

use Poker\Card\Card;
use Poker\Utilities\StringUtilities;

class PokerHand
{
    private int $value;
    private string $highCard;
    private string $highCardValue;
    private string $rank;
    private array $cardStrings = [];
    private array $cards = [];
    private array $utilizedCards = [];
    private array $rankValues = [];
    private array $cardValues = [];

    use StringUtilities;

    public function __construct(string $hand)
    {
        $this->rankValues = require 'PokerHandRankValuesArray.php';
        $this->cardValues = require __DIR__ . '/../Card/CardValuesArray.php';
        $this->cardStrings = explode(' ', $hand);

        foreach ($this->cardStrings as $string) {
            $this->cards[] = new Card($string);
        }
    }

    public function getRank(): string
    {
        // TODO: Complete all rules.
        if (
            $this->isRoyalFlush() ||
            // $this->isStraightFlush() ||
            // $this->isFourOfAKind() ||
            // $this->isFullHouse() ||
            // $this->isFlush() ||
            // $this->isStraight() ||
            // $this->isThreeOfAKind() ||
            // $this->isTwoPair() ||
            // $this->isPair() ||
            $this->isHighCard()
        ) {
            return $this->camelCaseToTitleCase($this->rank);
        }
    }

    private function setRank(string $rank): void
    {
        $this->rank = $rank;
        $this->value = $this->rankValues[$this->rank];
    }

    private function isRoyalFlush(): bool
    {
        // Conditions:
        // 1. All cards must have the same suit.
        // 2. There must be one each of cards of A, K, Q, J, and 10
        $suit = $this->cards[0]->getSuit();
        $counts = ['A' => 0, 'K' => 0, 'Q' => 0, 'J' => 0, '10' => 0];
        foreach ($this->cards as $card) {
            if ($suit !== $card->getSuit()) return false;
            $face = $card->getFace();
            if (array_key_exists($face, $counts)) $counts[$face]++;
        }

        // Validate that all five required cards exist and that there is one of each
        $cardsExistOnceArray = array_filter($counts, function ($v) {
            return $v === 1;
        });
        if (count($cardsExistOnceArray) !== count($counts)) return false;

        $this->setRank('royalFlush');

        return true;
    }

    private function isStraightFlush(): bool {}

    private function isFlush(): bool {}

    private function isFourOfAKind(): bool {}

    private function isStraight(): bool {}

    private function isFullHouse(): bool {}

    private function isThreeOfAKind(): bool {}

    private function isTwoPair(): bool {}

    private function isPair(): bool {}

    private function isHighCard(): bool
    {
        // Any poker hand with a single card will have a high card
        if (count($this->cards) === 0) {
            return false;
        }

        $this->setRank('highCard');

        return true;
    }
}
