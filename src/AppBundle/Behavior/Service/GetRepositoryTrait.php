<?php

namespace AppBundle\Behavior\Service;

use Doctrine\ORM\EntityManagerInterface;

/**
 * This trait is a simple wrapper to fool phpstan, for type-checking purpose
 */
trait GetRepositoryTrait
{
    private function getRepository(EntityManagerInterface $entityManager, string $entityClassName)
    {
        return $entityManager->getRepository($entityClassName);
    }
}
