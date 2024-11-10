<?php

namespace Poker\PokerHand;

use Poker\Card\Card;
use Poker\Utilities\StringUtilities;

class PokerHand
{
    private int $value;
    private string $highCard;  // TODO: Set highCard from utilized cards
    private string $highCardValue; // TODO: Determine the high card value from utilized cards
    private string $rank;
    private array $cardStrings = [];
    private array $cards = [];
    private array $cardMatches = [];
    private array $utilizedCards = []; // TODO: Set utilized cards in each ranking method
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

    /**
     * Get Rank
     * 
     * Determines the rank of the poker hand and return a
     * Title Case string denoting that rank.
     *
     * @return string | Title Case 
     */
    public function getRank(): string
    {
        // Determines if there are any card face value matches in the hand
        $this->setMatches();

        if (
            $this->isRoyalFlush() ||
            $this->isStraightFlush() ||
            $this->isFourOfAKind() ||
            $this->isFullHouse() ||
            $this->isFlush() ||
            $this->isStraight() ||
            $this->isThreeOfAKind() ||
            $this->isTwoPair() ||
            $this->isPair() ||
            $this->isHighCard()
        ) {
            return $this->camelCaseToTitleCase($this->rank);
        }
    }

    /**
     * Is Royal Flush - Rank Validator
     *
     * @return bool
     */
    private function isRoyalFlush(): bool
    {
        // Conditions:
        // 1. All cards must have the same suit
        // 2. There must be one each of cards of A, K, Q, J, and 10
        if (!$this->isValidFlush()) return false;

        // Validate that all five required cards exist and that there is one of each
        $counts = ['A' => 0, 'K' => 0, 'Q' => 0, 'J' => 0, '10' => 0];
        $this->incrementFaceValues($counts);
        $cardsExistOnceArray = array_filter($counts, function ($v) {
            return $v === 1;
        });
        if (count($cardsExistOnceArray) !== count($counts)) return false;

        $this->setRank('royalFlush');
        return true;
    }

    /**
     * Is Straight Flush - Rank Validator
     *
     * @return bool
     */
    private function isStraightFlush(): bool
    {
        // Conditions:
        // 1. All cards must be the same suit
        // 2. Have sequential values
        // 3. Handle Ace as low value
        $cardValuesAceLow = $this->cardValues;
        $cardValuesAceLow['A'] = 1;
        if (!$this->isValidFlush() || !$this->isValidStraight(true)) return false;

        $this->setRank('straightFlush');
        return true;
    }

    /**
     * Is Flush - Rank Validator
     *
     * @return bool
     */
    private function isFlush(): bool
    {
        // Conditions:
        // 1. All cards must be the same suit
        if (!$this->isValidFlush()) return false;

        $this->setRank('flush');
        return true;
    }

    /**
     * Is Straight - Rank Validator
     *
     * @return bool
     */
    private function isStraight(): bool
    {
        // Conditions:
        // 1. All cards have progressively incrementing values
        if (!$this->isValidStraight(true)) return false;

        $this->setRank('straight');
        return true;
    }

    /**
     * Is Four Of A Kind - Rank Validator
     *
     * @return bool
     */
    private function isFourOfAKind(): bool
    {
        // Conditions:
        // 1. Four face cards of matching value
        if (!$this->anyValueGreaterThan($this->cardMatches, 3)) return false;

        $this->setRank('fourOfAKind');
        return true;
    }

    /**
     * Is Full House - Rank Validator
     *
     * @return bool
     */
    private function isFullHouse(): bool
    {
        // Conditions:
        // 1. A set of three matching card values
        // 2. Another set of two matching card values
        $matches = $this->cardMatches;
        if (!$this->anyValueGreaterThan($matches, 2)) return false;

        $matchRequirements = [3 => 0, 2 => 0];

        foreach ($matches as $match => $number) {
            if ($number > 1) {
                ++$matchRequirements[$number];
            }
        }

        if (count(array_filter($matchRequirements, function ($value) {
            return $value === 1;
        })) !== count($matchRequirements)) {
            return false;
        }

        $this->setRank('fullHouse');
        return true;
    }

    /**
     * Is Three Of A Kind - Rank Validator
     *
     * @return bool
     */
    private function isThreeOfAKind(): bool
    {
        // Condition:
        // 1. A set of three matching card values
        if (!$this->anyValueGreaterThan($this->cardMatches, 2)) return false;

        $this->setRank('threeOfAKind');
        return true;
    }

    /**
     * Is Two Pair - Rank Validator
     *
     * @return bool
     */
    private function isTwoPair(): bool
    {
        // Condition:
        // 1. Two sets of two matching card values
        $matches = $this->cardMatches;
        if (!$this->anyValueGreaterThan($matches, 1)) return false;

        $matchRequirements = [2 => 0];

        foreach ($matches as $match => $number) {
            if ($number == 2) {
                $matchRequirements[$number] = ++$matchRequirements[$number];
            }
        }

        if ($matchRequirements[2] !== 2) return false;

        $this->setRank('twoPair');
        return true;
    }

    /**
     * Is Pair - Rank Validator
     *
     * @return bool
     */
    private function isPair(): bool
    {
        // Condition:
        // 1. A pair of matching card values
        if (!$this->anyValueGreaterThan($this->cardMatches, 1)) return false;

        $this->setRank('onePair');
        return true;
    }

    private function isHighCard(): bool
    {
        // Any poker hand with a single card will have a high card
        if (count($this->cards) === 0) {
            return false;
        }

        $this->setRank('highCard');
        return true;
    }

    /**
     * Is Valid Flush - Utility Validator
     * 
     * Determines if the hand is a valid "flush"; a hand
     * possessing cards of only a single suit.
     *
     * @return bool
     */
    private function isValidFlush(): bool
    {
        $suit = $this->cards[0]->getSuit();
        foreach ($this->cards as $card) {
            if ($suit !== $card->getSuit()) return false;
        }
        return true;
    }

    /**
     * Is Valid Straight - Utility Validator
     * 
     * Determines if the hand is a valid straight; a hand
     * with only incrementing values.
     * 
     * Examples:
     * - A, 2, 3, 4, 5
     * - Q, J, 10, 9, 8
     *
     * @param  bool $aceLow
     * @return bool
     */
    private function isValidStraight(bool $aceLow = false): bool
    {
        $cardValues = $this->getCardValuesArray();
        if ($aceLow && isset($cardValues['A'])) {
            $cardValues['A'] = 1;
        }
        asort($cardValues);
        $progressiveCount = (int) reset($cardValues);
        unset($cardValues[key($cardValues)]);
        foreach ($cardValues as $face => $value) {
            ++$progressiveCount;
            if ($progressiveCount != $value) return false;
        }
        return true;
    }

    /**
     * Set Matches
     * 
     * Determines and records the number of each value of card in the hand.
     * 
     * Example:
     * - "Ah 3s Qc Ad 6s" —> [A => 2, 6 => 1, 3 => 1, Q => 1]
     *
     * @return void
     */
    private function setMatches(): void
    {
        $currentFace = '';
        foreach ($this->cards as $card) {
            $currentFace = $card->getFace();
            $this->cardMatches[$currentFace] = (isset($this->cardMatches[$currentFace]))
                ? ++$this->cardMatches[$currentFace]
                : 1;
        }
    }

    /**
     * Any Value Greater Than - Utility Method
     *
     * Determines if any value found in an associative array of 
     * integer values is greater than the provided integer.
     * 
     * Utilized to determine if the matches required for a hand rank
     * exist in the `$matches` array.
     *
     * @param  array $array
     * @param  int $x
     * @return bool
     */
    private function anyValueGreaterThan(array $arrayOfInts, int $x): bool
    {
        return count(array_filter($arrayOfInts, function ($value) use ($x) {
            return $value > $x;
        })) > 0;
    }

    /**
     * Set Rank - Utility Method
     * 
     * Set the hand ranking string, as indexed in the PokerHandRankValuesArray,
     * as well as the hand value integer.
     *
     * @param  string $rank
     * @return void
     */
    private function setRank(string $rank): void
    {
        $this->rank = $rank;
        $this->value = $this->rankValues[$this->rank];
    }

    /**
     * Get Card Values Array - Utility Method
     *
     * Creates an array of `face => value` pairs to help determine
     * straights and other value-based calculations.
     * 
     * NOTE: Handles multiple cards with the same face/value by adding
     * an "_(int)" to the card-face key name.
     * 
     * @return array
     */
    private function getCardValuesArray(): array
    {
        $valuesArray = [];
        $copiesArray = [];
        foreach ($this->cards as $card) {
            $index = (string) $card->getFace();
            if (isset($valuesArray[$index])) {
                $copiesArray[$index] = isset($copiesArray[$index])
                    ? $copiesArray[$index]++
                    : 1;
                $index = $index . "_" . $copiesArray[$index];
            }
            $valuesArray[(string) $index] = $card->getValue();
        }
        return $valuesArray;
    }

    /**
     * Increment Face Values - Utility Method
     * 
     * References an array of `face => count` pairs and increments
     * the count for every matching card in a hand.
     * 
     * NOTE: Utilized to help determines if very specific card values
     * exist in a hand.
     *
     * @param  mixed &$faceValuesArray
     * @return void
     */
    private function incrementFaceValues(array &$faceValuesArray): void
    {
        foreach ($this->cards as $card) {
            if (array_key_exists($card->getFace(), $faceValuesArray)) {
                $faceValuesArray[$card->getFace()]++;
            }
        }
    }
}
