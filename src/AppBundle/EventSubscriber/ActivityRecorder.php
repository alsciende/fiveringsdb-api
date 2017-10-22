<?php

namespace AppBundle\EventSubscriber;

use AppBundle\Entity\Activity;
use AppBundle\Entity\Deck;
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

    public static function getSubscribedEvents()
    {
        return array(
            CommentAddedEvent::NAME => 'onCommentAdded',
        );
    }

    public function onCommentAdded(CommentAddedEvent $event)
    {
        $deck = $event->getComment()->getDeck();

        $activity = new Activity(Activity::TYPE_COMMENT_AUTHOR);
        $activity->setUser($deck->getUser());
        $activity->setText('You are the author');
        $this->entityManager->persist($activity);

        foreach($this->entityManager->getRepository(Deck::class)->findCommenters($deck) as $commenter) {
            if($commenter !== $deck->getUser()) {
                $activity = new Activity(Activity::TYPE_COMMENT_AUTHOR);
                $activity->setUser($commenter);
                $activity->setText('You are a commenter');
                $this->entityManager->persist($activity);
            }
        }

        $this->entityManager->flush();
    }
}
