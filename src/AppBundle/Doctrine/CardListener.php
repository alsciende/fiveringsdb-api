<?php

declare(strict_types=1);

namespace AppBundle\Doctrine;

use AppBundle\Entity\Card;
use AppBundle\Util\CanonicalFieldsUpdater;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

/**
 * Doctrine listener updating the canonical name field.
 */
class CardListener implements EventSubscriber
{
    private $canonicalFieldsUpdater;

    public function __construct (CanonicalFieldsUpdater $canonicalFieldsUpdater)
    {
        $this->canonicalFieldsUpdater = $canonicalFieldsUpdater;
    }

    public function getSubscribedEvents ()
    {
        return [
            'prePersist',
            'preUpdate',
        ];
    }

    /**
     * Pre persist listener based on doctrine common.
     */
    public function prePersist (LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        if ($object instanceof Card) {
            $this->updateCardFields($object);
        }
    }

    /**
     * Pre update listener based on doctrine common.
     */
    public function preUpdate (PreUpdateEventArgs $args)
    {
        $object = $args->getObject();
        if ($object instanceof Card && (
                $args->hasChangedField('name')
                || $args->hasChangedField('text')
            )) {
            $this->updateCardFields($object);
            $this->recomputeChangeSet($args->getEntityManager(), $object);
        }
    }

    /**
     * Updates the card properties.
     */
    private function updateCardFields (Card $card)
    {
        $this->canonicalFieldsUpdater->updateCanonicalFields($card);
    }

    /**
     * Recomputes change set for Doctrine implementations not doing it automatically after the event.
     */
    private function recomputeChangeSet (EntityManagerInterface $em, Card $card)
    {
        $meta = $em->getClassMetadata(Card::class);
        $em->getUnitOfWork()->recomputeSingleEntityChangeSet($meta, $card);
    }
}
