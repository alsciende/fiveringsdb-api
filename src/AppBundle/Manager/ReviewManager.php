<?php

namespace AppBundle\Manager;

/**
 * Description of ReviewManager
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class ReviewManager extends BaseManager
{

    /**
     * 
     * @param array $data
     * @param \AppBundle\Entity\User $user
     * @param \AppBundle\Entity\Card $card
     * @return \AppBundle\Entity\Review
     */
    public function create (array $data, \AppBundle\Entity\User $user, \AppBundle\Entity\Card $card)
    {
        $review = $this->serializer->denormalize($data, \AppBundle\Entity\Review::class);
        $review->setUser($user);
        $review->setCard($card);
        $this->entityManager->persist($review);
        return $review;
    }

    /**
     * 
     * @param array $data
     * @param int $id
     * @return \AppBundle\Entity\Review
     */
    public function update (array $data, $id)
    {
        $data['id'] = $id;
        $review = $this->serializer->denormalize($data, \AppBundle\Entity\Review::class);
        return $this->entityManager->merge($review);
    }

    public function findByCard (\AppBundle\Entity\Card $card)
    {
        return $this->entityManager->getRepository(\AppBundle\Entity\Review::class)->findBy(['card' => $card]);
    }

}
