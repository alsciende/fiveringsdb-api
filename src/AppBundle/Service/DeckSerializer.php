<?php

declare(strict_types=1);

namespace AppBundle\Service;

use AppBundle\Entity\Card;
use AppBundle\Entity\Deck;
use AppBundle\Entity\DeckCard;
use AppBundle\Exception\CardNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use JMS\Serializer\Context;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;

class DeckSerializer
{
    /** @var  EntityRepository */
    protected $repository;

    public function __construct (EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(Card::class);
    }

    public function deserializeDeckFromJson(JsonDeserializationVisitor $visitor, array $data, array $type): Deck
    {
        $deck = new Deck();

        $reflectionClass = new \ReflectionClass(Deck::class);

        /** @var \ReflectionProperty $reflectionProperty */
        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            if(isset($data[$reflectionProperty->getName()])) {
                $reflectionProperty->setAccessible(true);
                $reflectionProperty->setValue($deck, $data[$reflectionProperty->getName()]);
            }
        }

        foreach($data['cards'] as $card_code => $quantity) {
            $card = $this->repository->find($card_code);
            if($card === null) {
                throw new CardNotFoundException($card_code);
            }
            $deck->addDeckCard(new DeckCard($card, $quantity));
        }

        return $deck;
    }

    public function serializeDeckToJson(JsonSerializationVisitor $visitor, Deck $deck, array $type, Context $context)
    {
        $data = [
            'id' => $deck->getId(),
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

        if($visitor->getRoot() === null) {
            $visitor->setRoot($data);
        }

        return $data;
    }
}
