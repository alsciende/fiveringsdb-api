<?php

namespace AppBundle\Service\DeckSearch;

/**
 * Description of LastWeekPopularSearchService
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class LastWeekPopularSearchService extends AbstractPopularDeckSearchService
{
    static public function supports (): string
    {
        return 'week';
    }

    public function getNumberOfDays (): int
    {
        return 7;
    }
}