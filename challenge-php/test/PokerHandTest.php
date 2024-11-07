<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Poker\PokerHand\PokerHand;

class PokerHandTest extends TestCase
{
    /**
     * @test
     */
    public function itCanRankARoyalFlush()
    {
        $royalFlushHand = new PokerHand('As Ks Qs Js 10s');
        $this->assertEquals('Royal Flush', $royalFlushHand->getRank());
        $royalFlushHand = new PokerHand('As As Qs Js 10s');
        $this->assertNotEquals('Royal Flush', $royalFlushHand->getRank());
    }

    /**
     * @test
     */
    public function itCanRankAPair()
    {
        $pairHand = new PokerHand('Ah As 10c 7d 6s');
        $this->assertEquals('One Pair', $pairHand->getRank());
    }

    /**
     * @test
     */
    public function itCanRankTwoPair()
    {
        $twoPairHand = new PokerHand('Kh Kc 3s 3h 2d');
        $this->assertEquals('Two Pair', $twoPairHand->getRank());
    }

    /**
     * @test
     */
    public function itCanRankAFlush()
    {
        $flushHand = new PokerHand('Kh Qh 6h 2h 9h');
        $this->assertEquals('Flush', $flushHand->getRank());
    }

    /**
     * @test
     */
    public function itCanRankAFullHouse()
    {
        $fullHouseHand = new PokerHand('Qc 2h 2d 2c Qd');
        $this->assertEquals('Full House', $fullHouseHand->getRank());
    }

    /**
     * @test
     */
    public function itCanRankHighCard()
    {
        $highCardHand = new PokerHand('10c 4d 3c 6s 7d');
        $this->assertEquals('High Card', $highCardHand->getRank());
    }

    /**
     * @test
     */
    public function itCanRankThreeOfAKind()
    {
        $threeOfAKindHand = new PokerHand('3h 3s 6s Ah 3d');
        $this->assertEquals('Three of a Kind', $threeOfAKindHand->getRank());
    }

    /**
     * @test
     */
    public function itCanRankFourOfAKind()
    {
        $fourOfAKindHand = new PokerHand('2s 2c 2h As 2d');
        $this->assertEquals('Four of a Kind', $fourOfAKindHand->getRank());
    }

    /**
     * @test
     */
    public function itCanRankAStraight()
    {
        $straightHand = new PokerHand('4h 8c 6d 5d 7s');
        $this->assertEquals('Straight', $straightHand->getRank());
        $straightHand = new PokerHand('Qh 10h 9s Jc 8d');
        $this->assertEquals('Straight', $straightHand->getRank());
    }

    /**
     * @test
     */
    public function itCanRankAStraightFlush()
    {
        $straightFlushHand = new PokerHand('4c 8c 6c 5c 7c');
        $this->assertEquals('Straight Flush', $straightFlushHand->getRank());
        $straightFlushHand2 = new PokerHand('Qd 10d 9d Jd 8d');
        $this->assertEquals('Straight Flush', $straightFlushHand2->getRank());
    }
}
