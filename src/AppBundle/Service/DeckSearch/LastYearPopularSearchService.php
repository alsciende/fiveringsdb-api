<?php

namespace AppBundle\Service\DeckSearch;

/**
 * Description of LastYearPopularSearchService
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class LastYearPopularSearchService extends AbstractPopularDeckSearchService
{
    static public function supports (): string
    {
        return 'year';
    }

    public function getNumberOfDays (): int
    {
        return 365;
    }
}