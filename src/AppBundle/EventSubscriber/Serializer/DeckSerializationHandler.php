<?php

namespace AppBundle\EventSubscriber\Serializer;

use AppBundle\Entity\Deck;
use AppBundle\Service\DeckSerializer;
use JMS\Serializer\Context;
use JMS\Serializer\Exception\InvalidArgumentException;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;

/**
 * JMS Handler for Deck Serialization
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DeckSerializationHandler implements SubscribingHandlerInterface
{
    public static function getSubscribingMethods()
    {
        return [
            [
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format' => 'json',
                'type' => 'AppBundle\Entity\Deck',
                'method' => 'serializeDeckToJson',
            ],
            [
                'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                'format' => 'json',
                'type' => 'AppBundle\Entity\Deck',
                'method' => 'deserializeDeckFromJson',
            ],
        ];
    }

    /** @var DeckSerializer */
    private $serializer;

    public function __construct (DeckSerializer $serializer)
    {
        $this->serializer = $serializer;
    }

    public function deserializeDeckFromJson(JsonDeserializationVisitor $visitor, array $data, array $type): Deck
    {
        if($type['name'] !== Deck::class) {
            throw new InvalidArgumentException();
        }

        return $this->serializer->deserialize($data);
    }

    public function serializeDeckToJson(JsonSerializationVisitor $visitor, Deck $deck, array $type, Context $context)
    {
        $data = $this->serializer->serialize($deck);

        if($visitor->getRoot() === null) {
            $visitor->setRoot($data);
        }

        return $data;
    }
}