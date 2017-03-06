<?php

namespace Alsciende\SecurityBundle\Service;

/**
 * Description of UserManager
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class UserManager
{

    /** @var \Doctrine\ORM\EntityManager */
    private $em;

    /** @var \Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface */
    private $encoder;

    /** @var string */
    private $salt;

    public function __construct (\Doctrine\ORM\EntityManager $entityManager, \Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface $passwordEncoder, $salt)
    {
        $this->em = $entityManager;
        $this->encoder = $passwordEncoder;
        $this->salt = $salt;
    }

    public function createUser ($username, $plainPassword)
    {
        $user = new \AppBundle\Entity\User();
        $user->setUsername($username);
        $user->setPassword($this->encoder->encodePassword($user, $plainPassword));
        return $user;
    }
    
    public function updateUser(\AppBundle\Entity\User $user)
    {
        $this->em->persist($user);
        $this->em->flush();
    }

}
