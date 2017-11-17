<?php
/**
 * Created by PhpStorm.
 * User: cedric
 * Date: 16/11/17
 * Time: 11:06
 */

namespace AppBundle\Behavior\Service;

use Doctrine\ORM\EntityManagerInterface;

trait GetRepositoryTrait
{
    private function getRepository(EntityManagerInterface $entityManager, string $entityClassName)
    {
        return $entityManager->getRepository($entityClassName);
    }
}
