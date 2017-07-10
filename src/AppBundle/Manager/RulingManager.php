<?php

namespace AppBundle\Manager;
use AppBundle\Entity\Card;
use AppBundle\Entity\Ruling;
use AppBundle\Entity\User;

/**
 * Description of RulingManager
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class RulingManager extends BaseManager
{
    public function create (array $data, User $user, Card $card): Ruling
    {
        $ruling = $this->serializer->denormalize($data, Ruling::class);
        $ruling->setUser($user);
        $ruling->setCard($card);
        $this->entityManager->persist($ruling);
        return $ruling;
    }

    public function update (array $data, Ruling $ruling): Ruling
    {
        if(isset($data['text'])) {
            $ruling->setText($data['text']);
        }

        return $this->entityManager->merge($ruling);
    }

    public function findByCard (Card $card)
    {
        return $this->entityManager->getRepository(Ruling::class)->findBy(['card' => $card]);
    }
}
