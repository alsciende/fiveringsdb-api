<?php

namespace Tests\AlsciendeCerealBundle;

/**
 * Description of DomainFixtures
 *
 * @author Alsciende <alsciende@icloud.com>
 */
trait DomainFixtures
{

    function clearDatabase ()
    {

        $classNames = [
            \AppBundle\Entity\Clan::class,
            \AppBundle\Entity\Type::class,
            \AppBundle\Entity\Card::class,
            \AppBundle\Entity\Cycle::class,
            \AppBundle\Entity\Pack::class
        ];

        foreach($classNames as $className) {
            foreach($this->em->getRepository($className)->findAll() as $entity) {
                $this->em->remove($entity);
            }
        }

        $this->em->flush();
    }

    function createCrab ()
    {
        $clan = new \AppBundle\Entity\Clan();
        $clan->setCode('crab');
        $clan->setName("Crab");
        $this->em->persist($clan);
        $this->em->flush();
        return $clan;
    }

    function createStronghold ()
    {
        $type = new \AppBundle\Entity\Type();
        $type->setCode('stronghold');
        $type->setName("Stronghold");
        $this->em->persist($type);
        $this->em->flush();
        return $type;
    }

    function createCrabFortress ()
    {
        $card = new \AppBundle\Entity\Card();
        $card->setCode('01001');
        $card->setName("The Impregnable Fortress of the Crab");
        $card->setClan($this->createCrab());
        $card->setType($this->createStronghold());
        $this->em->persist($card);
        $this->em->flush();
        return $card;
    }

    function createCycleCore ()
    {
        $cycle = new \AppBundle\Entity\Cycle();
        $cycle->setCode('core');
        $cycle->setName("Core Set");
        $cycle->setPosition(1);
        $cycle->setSize(1);
        $this->em->persist($cycle);
        $this->em->flush();
        return $cycle;
    }

    function createPackCore()
    {
        $pack = new \AppBundle\Entity\Pack();
        $pack->setCode('core');
        $pack->setName("Core Set");
        $pack->setPosition(1);
        $pack->setSize(350);
        $this->em->persist($pack);
        $this->em->flush();
        return $pack;
    }
    
    function createPackSlotCrabFortress ()
    {
        $packslot = new \AppBundle\Entity\PackSlot();
        $packslot->setCard($this->createCrabFortress());
        $packslot->setPack($this->createPackCore());
        $this->em->persist($packslot);
        $this->em->flush();
        return $packslot;
    }
}
