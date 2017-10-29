<?php

namespace AppBundle\Service\DeckSearch;

/**
 * Description of LastMonthPopularSearchService
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class LastMonthPopularSearchService extends AbstractPopularDeckSearchService
{
    static public function supports (): string
    {
        return 'month';
    }

    public function getFreshness (): int
    {
        return 2;
    }
}