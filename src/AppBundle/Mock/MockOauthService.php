<?php
/**
 * Created by PhpStorm.
 * User: cedric
 * Date: 22/02/18
 * Time: 10:58
 */

namespace AppBundle\Mock;

use AppBundle\Behavior\Service\OauthServiceInterface;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

use Ramsey\Uuid\Uuid;

/**
 */
class MockOauthService implements OauthServiceInterface
{
    /** @var EntityManagerInterface $entityManager */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getUserData(string $credentials): ?array
    {
        list($type, $username) = explode(' ', $credentials, 2);

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $username]);

        if($user instanceof User) {
            return [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
            ];
        }

        if(strpos('user', $username) === 0) {
            return [
                'id' => Uuid::uuid4(),
                'username' => $username,
            ];
        }

        return null;
    }
}
