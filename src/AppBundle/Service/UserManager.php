<?php

namespace AppBundle\Service;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @author Alsciende <alsciende@icloud.com>
 */
class UserManager
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var UserPasswordEncoderInterface */
    private $encoder;

    /** @var string */
    private $salt;

    public function __construct (EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder, $salt)
    {
        $this->em = $entityManager;
        $this->encoder = $passwordEncoder;
        $this->salt = $salt;
    }

    public function findUserByUsername (string $username): ?User
    {
        return $this->em->getRepository(User::class)->findOneBy(['username' => $username]);
    }
    
    public function createUser (string $username, string $plainPassword): User
    {
        $user = new User();
        $user->setUsername($username);
        $user->setPassword($this->encoder->encodePassword($user, $plainPassword));
        return $user;
    }
    
    public function updateUser(User $user)
    {
        $this->em->persist($user);
        $this->em->flush();
    }
}
