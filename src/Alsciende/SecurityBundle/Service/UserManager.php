<?php

namespace Alsciende\SecurityBundle\Service;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Description of UserManager
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class UserManager
{

    /** @var EntityManager */
    private $em;

    /** @var UserPasswordEncoderInterface */
    private $encoder;

    /** @var string */
    private $salt;

    public function __construct (EntityManager $entityManager, UserPasswordEncoderInterface $passwordEncoder, $salt)
    {
        $this->em = $entityManager;
        $this->encoder = $passwordEncoder;
        $this->salt = $salt;
    }

    /**
     * 
     * @param string $username
     * @return User
     */
    public function findUserByUsername ($username)
    {
        return $this->em->getRepository(User::class)->findOneBy(['username' => $username]);
    }
    
    /**
     * 
     * @param string $username
     * @param string $plainPassword
     * @return User
     */
    public function createUser ($username, $plainPassword)
    {
        $user = new User();
        $user->setUsername($username);
        $user->setPassword($this->encoder->encodePassword($user, $plainPassword));
        return $user;
    }
    
    /**
     * 
     * @param User $user
     */
    public function updateUser(User $user)
    {
        $this->em->persist($user);
        $this->em->flush();
    }

}
