<?php

namespace AppBundle\Service;

use AppBundle\Behavior\Service\GetRepositoryTrait;
use AppBundle\Entity\Activity;
use AppBundle\Entity\Deck;
use AppBundle\Entity\User;
use AppBundle\Repository\ActivityRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Description of ActivityManager
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class ActivityManager
{
    use GetRepositoryTrait;

    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var ActivityRepository */
    protected $activityRepository;

    public function __construct (EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->activityRepository = $this->getRepository($entityManager, Activity::class);
    }

    public function getActivity (User $user = null)
    {
        $list = $this->getPublicActivity();
        if($user instanceof User) {
            $list = array_merge($list, $this->getPersonalActivity($user));
        }
        usort($list, function (Activity $a, Activity $b) {
            return $b->getCreatedAt() <=> $a->getCreatedAt();
        });

        return $list;
    }

    /** @return Activity[] */
    private function getPersonalActivity (User $user): array
    {
        return array_map(function (Activity $activity) {
            return $activity->setPersonal(true);
        }, $this
            ->activityRepository
            ->findForUser($user, 10));
    }

    /** @return Activity[] */
    private function getPublicActivity(): array
    {
        $decks = $this
            ->entityManager
            ->getRepository(Deck::class)
            ->findBy(
                ['published' => true],
                ['publishedAt' => 'DESC'],
                10
            );

        return array_map(function (Deck $deck) {
            $activity = new Activity(
                Activity::TYPE_DECKLIST_PUBLISHED,
                $deck,
                $deck->getUser(),
                $deck->getPublishedAt()
            );
            return $activity->setPersonal(false);
        }, $decks);
    }
}