<?php

namespace AppBundle\Service;

use AppBundle\Behavior\Service\GetRepositoryTrait;
use AppBundle\Entity\Token;
use AppBundle\Entity\User;
use AppBundle\Form\Type\TokenType;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\ArrayTransformerInterface;
use JMS\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\FormFactory;

/**
 * @author Alsciende <alsciende@icloud.com>
 */
class TokenManager
{
    use GetRepositoryTrait;

    /** @var EntityManagerInterface $entityManager */
    private $entityManager;

    public function __construct (EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $token
     * @return Token|null
     */
    public function findToken (string $token): ?Token
    {
        return $this->entityManager->find(Token::class, $token);
    }

    /**
     * @param array $criteria
     * @return Token|null
     */
    public function findTokenBy(array $criteria): ?Token
    {
        return $this->entityManager->getRepository(Token::class)->findOneBy($criteria);
    }

    /**
     * @param array $data
     * @return Token|null
     */
    public function createToken (array $data): ?Token
    {
        return new Token(
            $data['access_token'],
            $data['expires_in'],
            $data['token_type'],
            $data['scope'],
            $data['refresh_token']
        );
    }

    /**
     * @param Token $token
     */
    public function updateToken (Token $token)
    {
        $this->entityManager->persist($token);
        $this->entityManager->flush();
    }
}