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

    public function getUserData(string $credentials): ?string
    {
        list($type, $username) = explode(' ', $credentials, 2);

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $username]);

        if($user instanceof User) {
            return json_encode([
                'id' => $user->getId(),
                'username' => $user->getUsername(),
            ]);
        }

        if(strpos($username, 'user') === 0) {
            return json_encode([
                'id' => Uuid::uuid4(),
                'username' => $username,
            ]);
        }

        return null;
    }
}
