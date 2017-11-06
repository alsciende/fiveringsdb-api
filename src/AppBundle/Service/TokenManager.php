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

    /** @var int */
    private $ttl;

    public function __construct (EntityManagerInterface $entityManager, int $ttl)
    {
        $this->em = $entityManager;
        $this->ttl = $ttl;
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
        $token->setCreatedAt(new \DateTime());
        $expiresAt = clone($token->getCreatedAt());
        $expiresAt->add(\DateInterval::createFromDateString($this->ttl . ' seconds'));
        $token->setExpiresAt($expiresAt);

        return $token;
    }

    public function updateToken (Token $token)
    {
        $this->em->persist($token);
        $this->em->flush();
    }
}