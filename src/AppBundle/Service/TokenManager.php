<?php

namespace AppBundle\Service;

use AppBundle\Entity\Token;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Description of TokenManager
 *
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

    /**
     *
     * @param string $token
     * @return Token
     */
    public function findToken ($token)
    {
        return $this->em->getRepository(Token::class)->find($token);
    }
}