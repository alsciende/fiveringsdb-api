<?php

declare(strict_types=1);

namespace AppBundle\Service;

use AppBundle\Entity\Deck;
use Symfony\Component\Form\DataTransformerInterface;

class DeckSerializer
{
    /** @var DataTransformerInterface */
    private $transformer;

    public function __construct (DataTransformerInterface $transformer)
    {
        $this->transformer = $transformer;
    }

    public function deserialize (array $data): Deck
    {
        $deck = new Deck();

        $reflectionClass = new \ReflectionClass(Deck::class);

        /** @var \ReflectionProperty $reflectionProperty */
        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            if (isset($data[$reflectionProperty->getName()])) {
                $reflectionProperty->setAccessible(true);
                $reflectionProperty->setValue($deck, $data[$reflectionProperty->getName()]);
            }
        }

        $deck->setDeckCards($this->transformer->reverseTransform($data['cards']));

        return $deck;
    }

    public function serialize (Deck $deck)
    {
        return [
            'id'          => $deck->getId(),
            'name'        => $deck->getName(),
            'description' => $deck->getDescription(),
            'format'      => $deck->getFormat(),
            'cards'       => $this->transformer->transform($deck->getDeckCards()),
            'created_at'  => $deck->getCreatedAt()->format('c'),
            'user_id'     => $deck->getUser() ? $deck->getUser()->getId() : null,
            'nb_likes'    => $deck->getNbLikes(),
            'version'     => $deck->getMajorVersion() . '.' . $deck->getMinorVersion(),
            'problem'     => $deck->getProblem(),
            'strain'      => $deck->getStrain() ? $deck->getStrain()->getId() : null,
        ];
    }
}
