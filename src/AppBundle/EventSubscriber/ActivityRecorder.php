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
        $data = [
            'deck' => [
                'id'   => $deck->getId(),
                'name' => $deck->getName(),
            ],
            'user' => [
                'id'   => $comment->getUser()->getId(),
                'name' => $comment->getUser()->getUsername(),
            ],
        ];

        $activity = new Activity(Activity::TYPE_COMMENT_AUTHOR);
        $activity->setUser($deck->getUser());
        $activity->setData($data);
        $this->entityManager->persist($activity);

        foreach ($this->entityManager->getRepository(Deck::class)->findCommenters($deck) as $commenter) {
            if ($commenter !== $deck->getUser()) {
                $activity = new Activity(Activity::TYPE_COMMENT_PARTICIPANT);
                $activity->setUser($commenter);
                $activity->setData($data);
                $this->entityManager->persist($activity);
            }
        }

        $this->entityManager->flush();
    }
}
