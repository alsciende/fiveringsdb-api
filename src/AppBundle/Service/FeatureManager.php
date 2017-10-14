<?php

namespace AppBundle\Service;

use AppBundle\Entity\Deck;
use AppBundle\Entity\Feature;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Description of FeatureManager
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class FeatureManager
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var int */
    private $period;

    public function __construct (EntityManagerInterface $entityManager, int $period)
    {
        $this->entityManager = $entityManager;
        $this->period = $period;
    }

    public function getElectedFeatures ()
    {
        $featureRepository = $this->entityManager->getRepository(Feature::class);
        $date = $featureRepository->findLastDate();
        if ($date instanceof \DateTime) {
            return $featureRepository->findBy(['date' => $date]);
        }

        return [];
    }

    public function electFeatures ()
    {
        $today = new \DateTime();

        foreach ($this->entityManager->getRepository(Deck::class)->findClans() as $clan) {
            $deck = $this
                ->entityManager
                ->getRepository(Deck::class)
                ->findBestDeckForClan($clan, $this->period);
            if ($deck instanceof Deck) {
                $this->entityManager->persist(new Feature($clan, $today, $deck));
            }
        }

        $this->entityManager->flush();
    }
}