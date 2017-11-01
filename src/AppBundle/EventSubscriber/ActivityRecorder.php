<?php

namespace AppBundle\EventSubscriber;

use AppBundle\Entity\Activity;
use AppBundle\Entity\Deck;
use AppBundle\Entity\Notification;
use AppBundle\Event\CommentAddedEvent;
use AppBundle\Repository\ActivityRepository;
use AppBundle\Repository\DeckRepository;
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

    /** @var DeckRepository */
    private $deckRepository;

    public function __construct (
        EntityManagerInterface $entityManager,
        DeckRepository $deckRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->deckRepository = $deckRepository;
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
