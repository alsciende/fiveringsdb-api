<?php

namespace AppBundle\Service;

use AppBundle\Entity\Deck;
use AppBundle\Entity\Feature;
use AppBundle\Repository\DeckRepository;
use AppBundle\Repository\FeatureRepository;
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

    /** @var FeatureRepository */
    private $featureRepository;

    /** @var DeckRepository */
    private $deckRepository;

    /** @var int */
    private $period;

    public function __construct (
        EntityManagerInterface $entityManager,
        FeatureRepository $featureRepository,
        DeckRepository $deckRepository,
        int $period
    ) {
        $this->entityManager = $entityManager;
        $this->featureRepository = $featureRepository;
        $this->deckRepository = $deckRepository;
        $this->period = $period;
    }

    public function getElectedFeatures ()
    {
        $date = $this->featureRepository->findLastDate();
        if ($date instanceof \DateTime) {
            return $this->featureRepository->findBy(['date' => $date]);
        }

        return [];
    }

    public function electFeatures ()
    {
        $today = new \DateTime();

        foreach ($this->deckRepository->findClans() as $clan) {
            $deck = $this->deckRepository->findBestDeckForClan($clan, $this->period);
            if ($deck instanceof Deck) {
                $this->entityManager->persist(new Feature($clan, $today, $deck));
            }
        }

        $this->entityManager->flush();
    }
}