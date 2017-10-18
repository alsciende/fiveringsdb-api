<?php

namespace AppBundle\Form\DataTransformer;

use AppBundle\Entity\Card;
use AppBundle\Entity\DeckCard;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class CardSlotsTransformer implements DataTransformerInterface
{
    /** @var  ObjectRepository */
    protected $repository;

    public function __construct (EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(Card::class);
    }

    /**
     * @param Collection|DeckCard[] $deckCards
     * @return array
     */
    public function transform ($deckCards)
    {
        $data = [];
        foreach ($deckCards as $deckCard) {
            $data[$deckCard->getCard()->getId()] = $deckCard->getQuantity();
        }

        return $data;
    }

    /**
     * @param array $data
     * @return Collection|DeckCard[] $deckCards
     */
    public function reverseTransform ($data)
    {
        $deckCards = [];
        if (empty($data)) {
            return $deckCards;
        }

        foreach ($data as $card_id => $quantity) {
            $card = $this->repository->find($card_id);
            if ($card === null) {
                throw new TransformationFailedException(
                    sprintf(
                        'A card with the identifier "%s" does not exist!',
                        $card_id
                    )
                );
            }
            $deckCards[] = new DeckCard($card, $quantity);
        }

        return new ArrayCollection($deckCards);
    }
}
