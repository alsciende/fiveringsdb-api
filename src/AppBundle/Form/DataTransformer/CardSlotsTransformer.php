<?php

namespace AppBundle\Form\DataTransformer;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Card;
use AppBundle\Entity\DeckCard;

class CardSlotsTransformer implements DataTransformerInterface
{
    /** @var  EntityRepository */
    protected $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(Card::class);
      }

    public function transform($deckCards)
    {
        $data = [];
        foreach($deckCards as $deckCard) {
            $data[$deckCard->getCard()->getId()] = $deckCard->getQuantity();
        }

        return $data;
    }

    public function reverseTransform($data)
    {
      $deckCards = [];
        foreach($data as $card_id => $quantity) {
            $card = $this->repository->find($card_id);
            if($card === null) {
                throw new TransformationFailedException(sprintf(
                    'A card with the identifier "%s" does not exist!',
                    $card_id
                ));
            }
            $deckCards[] = new DeckCard($card, $quantity);
        }

        return new ArrayCollection($deckCards);
    }
}
