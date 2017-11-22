<?php

namespace AppBundle\Behavior\Service;

use Doctrine\ORM\EntityManagerInterface;

trait GetRepositoryTrait
{
    private function getRepository(EntityManagerInterface $entityManager, string $entityClassName)
    {
        return $entityManager->getRepository($entityClassName);
    }
}
