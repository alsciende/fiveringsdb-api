<?php

namespace AppBundle\Service;

use AppBundle\Search\DeckSearch;
use AppBundle\Service\DeckSearch\DeckSearchServiceInterface;

/**
 * Description of DeckSearchService
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DeckSearchService
{

    /** @var DeckSearchServiceInterface[] */
    private $services;

    public function __construct ()
    {
        $this->services = [];
    }

    /**
     * Called by the CompilerPass to get all the DeckChecks in services.yml
     */
    public function addService (DeckSearchServiceInterface $service)
    {
        $this->services[$service::supports()] = $service;
    }

    /**
     * @return string[]
     */
    public function getSupported (): array
    {
        return array_keys($this->services);
    }

    /**
     * @param DeckSearch $search
     * @return bool
     */
    public function search (DeckSearch $search)
    {
        if (key_exists($search->getSort(), $this->services) === false) {
            throw new \InvalidArgumentException('Unknown deck sort ' . $search->getSort());
        }

        $handler = $this->services[$search->getSort()];

        $handler->search($search);

        return true;
    }
}