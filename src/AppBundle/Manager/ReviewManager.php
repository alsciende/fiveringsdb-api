<?php

namespace AppBundle\Manager;
use AppBundle\Entity\Card;
use AppBundle\Entity\Review;
use AppBundle\Entity\User;

/**
 * Description of ReviewManager
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class ReviewManager extends BaseManager
{
    public function create (array $data, User $user, Card $card): Review
    {
        $review = $this->serializer->denormalize($data, Review::class);
        $review->setUser($user);
        $review->setCard($card);
        $this->entityManager->persist($review);
        return $review;
    }

    public function update (array $data, Review $review): Review
    {
        if(isset($data['text'])) {
            $review->setText($data['text']);
        }

        return $this->entityManager->merge($review);
    }

    public function findByCard (Card $card)
    {
        return $this->entityManager->getRepository(Review::class)->findBy(['card' => $card]);
    }
}
