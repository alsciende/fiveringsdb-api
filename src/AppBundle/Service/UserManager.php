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

    /**
     * @param string $username
     * @return User|null
     */
    public function findUserByUsername (string $username): ?User
    {
        $repository = $this->getRepository($this->entityManager, User::class);

        return $repository->findOneBy(['username' => $username]);
    }

    /**
     * @param string $id
     * @return User|null
     */
    public function findUserById (string $id): ?User
    {
        return $this->entityManager->find(User::class, $id);
    }

    /**
     * @param string $id
     * @param string $username
     * @return User
     */
    public function createUser (string $id, string $username): User
    {
        $user = new User();
        $user->setId($id)
             ->setUsername($username)
             ->setPassword($username)
             ->setEnabled(true);

        return $user;
    }

    /**
     * @param User $user
     */
    public function updateUser (User $user)
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    /**
     * @param array $userData
     * @return User
     */
    public function findOrCreateUser(array $userData)
    {
        $user = $this->findUserById($userData['id']);
        if ($user instanceof User) {
            return $user;
        }

        return $this->createUser($userData['id'], $userData['username']);
    }
}
