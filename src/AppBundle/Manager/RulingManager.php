<?php

namespace AppBundle\Manager;

/**
 * Description of RulingManager
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class RulingManager extends BaseManager
{

    /**
     * 
     * @param array $data
     * @param \AppBundle\Entity\User $user
     * @param \AppBundle\Entity\Card $card
     * @return \AppBundle\Entity\Ruling
     */
    public function create (array $data, \AppBundle\Entity\User $user, \AppBundle\Entity\Card $card)
    {
        $ruling = $this->serializer->denormalize($data, \AppBundle\Entity\Ruling::class);
        $ruling->setUser($user);
        $ruling->setCard($card);
        $this->entityManager->persist($ruling);
        return $ruling;
    }

    /**
     * 
     * @param array $data
     * @param int $id
     * @return \AppBundle\Entity\Ruling
     */
    public function update (array $data, $id)
    {
        $data['id'] = $id;
        $ruling = $this->serializer->denormalize($data, \AppBundle\Entity\Ruling::class);
        return $this->entityManager->merge($ruling);
    }

    public function findByCard (\AppBundle\Entity\Card $card)
    {
        return $this->entityManager->getRepository(\AppBundle\Entity\Ruling::class)->findBy(['card' => $card]);
    }

}
