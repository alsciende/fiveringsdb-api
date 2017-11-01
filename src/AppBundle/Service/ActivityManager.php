<?php

namespace AppBundle\Service;

use AppBundle\Entity\Activity;
use AppBundle\Entity\Deck;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Description of ActivityManager
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class ActivityManager
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    public function __construct (EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getActivity (User $user)
    {
        $personal = $this->getPersonalActivity($user);
        $public = $this->getPublicActivity();
        $list = array_merge($personal, $public);
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
            ->entityManager
            ->getRepository(Activity::class)
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