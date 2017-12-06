<?php

namespace AppBundle\EventSubscriber;

use AppBundle\Behavior\Service\GetRepositoryTrait;
use AppBundle\Entity\Activity;
use AppBundle\Entity\Deck;
use AppBundle\Entity\Notification;
use AppBundle\Event\CommentAddedEvent;
use AppBundle\Repository\DeckRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Description of ActivityEventSubscriber
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class ActivityRecorder implements EventSubscriberInterface
{
    use GetRepositoryTrait;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var DeckRepository */
    private $deckRepository;

    public function __construct (EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->deckRepository = $this->getRepository($entityManager, Deck::class);
    }

    public static function getSubscribedEvents ()
    {
        return [
            CommentAddedEvent::NAME => 'onCommentAdded',
        ];
    }

    public function onCommentAdded (CommentAddedEvent $event)
    {
        $comment = $event->getComment();
        $user = $comment->getUser();
        $deck = $comment->getDeck();

        $activity = new Activity(Activity::TYPE_COMMENT_ADDED, $deck, $comment->getUser());
        $activity->addNotification(new Notification($deck->getUser(), $activity));

        foreach ($this->deckRepository->findCommenters($deck) as $commenter) {
            if ($commenter !== $deck->getUser() && $commenter !== $user) {
                $activity->addNotification(new Notification($commenter, $activity));
            }
        }

        $this->entityManager->persist($activity);
        $this->entityManager->flush();
    }
}
