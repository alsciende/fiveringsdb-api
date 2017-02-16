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

        foreach($this->em->getRepository('AppBundle:Clan')->findAll() as $entity) {
            $this->em->remove($entity);
        }
        foreach($this->em->getRepository('AppBundle:Type')->findAll() as $entity) {
            $this->em->remove($entity);
        }
        foreach($this->em->getRepository('AppBundle:Card')->findAll() as $entity) {
            $this->em->remove($entity);
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
    }

    function createStronghold ()
    {
        $type = new \AppBundle\Entity\Type();
        $type->setCode('stronghold');
        $type->setName("Stronghold");
        $this->em->persist($type);
        $this->em->flush();
    }

}
