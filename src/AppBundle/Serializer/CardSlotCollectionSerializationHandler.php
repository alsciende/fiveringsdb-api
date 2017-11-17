<?php

namespace AppBundle\Serializer;

use AppBundle\Form\DataTransformer\CardSlotsTransformer;
use AppBundle\Model\CardSlotCollectionDecorator;
use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;

/**
 * Description of CardSlotCollectionSerializationHandler
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class CardSlotCollectionSerializationHandler implements SubscribingHandlerInterface
{
    public static function getSubscribingMethods ()
    {
        return [
            [
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format'    => 'json',
                'type'      => CardSlotCollectionDecorator::class,
                'method'    => 'serializeToJson',
            ],
            [
                'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                'format'    => 'json',
                'type'      => CardSlotCollectionDecorator::class,
                'method'    => 'deserializeFromJson',
            ],
        ];
    }

    /** @var CardSlotsTransformer */
    private $transformer;

    public function __construct (CardSlotsTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function deserializeFromJson (JsonDeserializationVisitor $visitor, array $data, array $type): CardSlotCollectionDecorator
    {
        if ($type['name'] !== CardSlotCollectionDecorator::class) {
            throw new \InvalidArgumentException();
        }

        return new CardSlotCollectionDecorator($this->transformer->reverseTransform($data)->toArray());
    }

    public function serializeToJson (JsonSerializationVisitor $visitor, CardSlotCollectionDecorator $collection, array $type, Context $context)
    {
        $data = $this->transformer->transform($collection);

        if ($visitor->getRoot() === null) {
            $visitor->setRoot($data);
        }

        return $data;
    }
}
