<?php

namespace AppBundle\EventSubscriber;

use AppBundle\Entity\Activity;
use AppBundle\Entity\Deck;
use AppBundle\Entity\Notification;
use AppBundle\Event\CommentAddedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Description of ActivityEventSubscriber
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class ActivityRecorder implements EventSubscriberInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var LoggerInterface */
    private $logger;

    public function __construct (EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
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
        $deck = $comment->getDeck();

        $activity = new Activity(Activity::TYPE_COMMENT_ADDED, $deck);
        $activity->addNotification(new Notification($deck->getUser(), $activity));

        foreach ($this->entityManager->getRepository(Deck::class)->findCommenters($deck) as $commenter) {
            if ($commenter !== $deck->getUser()) {
                $activity->addNotification(new Notification($commenter, $activity));
            }
        }

        $this->entityManager->persist($activity);
        $this->entityManager->flush();
    }
}
