<?php

namespace AppBundle\Service;

use AppBundle\Entity\Token;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;


/**
 * @author Alsciende <alsciende@icloud.com>
 */
class TokenManager
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct (EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function findToken (string $token): ?Token
    {
        return $this->em->getRepository(Token::class)->find($token);
    }

    public function createToken (string $value, User $user): Token
    {
        $token = new Token();
        $token->setId($value);
        $token->setUser($user);

        return $token;
    }

    public function updateToken (Token $token)
    {
        $this->em->persist($token);
        $this->em->flush();
    }
}