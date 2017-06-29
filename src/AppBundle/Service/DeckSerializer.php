<?php

declare(strict_types=1);

namespace AppBundle\Service;

use AppBundle\Entity\Card;
use AppBundle\Entity\Deck;
use AppBundle\Entity\DeckCard;
use JMS\Serializer\Context;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;

class DeckSerializer
{
    public function deserializeDeckFromJson(JsonDeserializationVisitor $visitor, array $data, array $type): Deck
    {
        $deck = new Deck();
        $deck->setName($data['name']);
        if(isset($data['description'])) {
            $deck->setDescription($data['description']);
        }
        if(isset($data['lineage'])) {
            $deck->setLineage($data['lineage']);
        }
        if(isset($data['genus'])) {
            $deck->setGenus($data['genus']);
        }

        foreach($data['cards'] as $card_code => $quantity) {
            $deck->addDeckCard(new DeckCard((new Card())->setCode($card_code), $quantity));
        }

        return $deck;
    }

    public function serializeDeckToJson(JsonSerializationVisitor $visitor, Deck $deck, array $type, Context $context)
    {
        $data = [
            'name' => $deck->getName(),
            'description' => $deck->getDescription(),
            'cards' => [],
            'created_at' => $deck->getCreatedAt()->format('c'),
            'user_id' => $deck->getUser() ? $deck->getUser()->getId() : null,
            'nb_likes' => $deck->getNbLikes(),
            'version' => $deck->getMajorVersion() . '.' . $deck->getMinorVersion(),
            'problem' => $deck->getProblem(),
            'lineage' =>$deck->getLineage(),
            'genus' => $deck->getGenus(),
        ];

        /** @var DeckCard $deckCard */
        foreach($deck->getDeckCards() as $deckCard) {
            $data['cards'][$deckCard->getCard()->getCode()] = $deckCard->getQuantity();
        }

        $visitor->setRoot($data);
    }
}
