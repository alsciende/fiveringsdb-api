<?php

namespace AppBundle\Service\DeckSearch;

/**
 * Description of PopularDeckSearchServiceInterface
 *
 * @author Alsciende <alsciende@icloud.com>
 */
interface PopularDeckSearchServiceInterface extends DeckSearchServiceInterface
{
    public function getFreshness (): int;
}