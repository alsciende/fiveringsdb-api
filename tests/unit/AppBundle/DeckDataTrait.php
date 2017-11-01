<?php

declare(strict_types=1);

namespace Tests\Unit\AppBundle;

trait DeckDataTrait
{
    /** @var array */
    private $deckData = [
        'name' => 'PHPUnit Test Deck',
        'description' => '',
        'format' => 'standard',
        'cards' => [
            'shizuka-toshi' => 1,
            'ancestral-lands' => 1, // Earth Province
            'elemental-fury' => 1, // Water Province
            'the-art-of-peace' => 1, // Air Province
            'night-raid' => 1, // Fire Province
            'pilgrimage' => 1, // Void Province
            // start of Dynasty
            'artisan-academy' => 3,
            'asahina-artisan' => 3,
            'asahina-storyteller' => 3,
            'daidoji-nerishma' => 3,
            'doji-challenger' => 3,
            'doji-gift-giver' => 3,
            'doji-hotaru' => 3,
            'doji-whisperer' => 3,
            'guest-of-honor' => 3,
            'kakita-asami' => 3,
            'kakita-kaezin' => 3,
            'otomo-courtier' => 3,
            'savvy-politician' => 3,
            'wandering-ronin' => 3,
            // start of Conflict
            'above-question' => 3,
            'admit-defeat' => 3,
            'banzai' => 3,
            'cloud-the-mind' => 3,
            'duelist-training' => 3,
            'fallen-in-battle' => 3,
            'for-shame' => 3,
            'good-omen' => 3,
            'height-of-fashion' => 3,
            'noble-sacrifice' => 3,
            'spies-at-court' => 3,
            'steward-of-law' => 3,
            'the-perfect-gift' => 3,
            'voice-of-honor' => 3,
            'way-of-the-crane' => 3,
        ]
    ];

    public function getDeckData(array $diff = [])
    {
        $data = $this->deckData;
        foreach($diff as $card_id => $quantity_modifier)
        {
            $data[$card_id] += $quantity_modifier;
        }

        return $data;
    }
}
