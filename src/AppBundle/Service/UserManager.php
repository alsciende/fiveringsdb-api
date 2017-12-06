<?php

namespace AppBundle\Service;

use AppBundle\Behavior\Service\GetRepositoryTrait;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Alsciende <alsciende@icloud.com>
 */
class UserManager
{
    use GetRepositoryTrait;

    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct (EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function findUserByUsername (string $username): ?User
    {
        $repository = $this->getRepository($this->entityManager, User::class);

        return $repository->findOneBy(['username' => $username]);
    }

    public function findUserById (string $id): ?User
    {
        return $this->entityManager->find(User::class, $id);
    }

    public function createUser (string $id, string $username): User
    {
        $user = new User();
        $user->setId($id)
             ->setUsername($username)
             ->setPassword($username)
             ->setEnabled(true);

        return $user;
    }

    public function updateUser (User $user)
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
