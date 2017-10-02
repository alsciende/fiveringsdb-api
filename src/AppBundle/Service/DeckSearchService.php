<?php

namespace AppBundle\Service;

use AppBundle\Search\DeckSearch;
use AppBundle\Service\DeckSearch\DeckSearchInterface;

/**
 * Description of DeckSearchService
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DeckSearchService
{

    /** @var DeckSearchInterface[] */
    private $services;

    public function __construct ()
    {
        $this->services = [];
    }

    /**
     * Called by the CompilerPass to get all the DeckChecks in services.yml
     */
    public function addService (DeckSearchInterface $service)
    {
        $this->services[$service::supports()] = $service;
    }

    /**
     * @param DeckSearch $search
     * @return bool
     */
    public function search(DeckSearch $search)
    {
        if(key_exists($search->getSort(), $this->services) === false) {
            throw new \InvalidArgumentException('Unknown deck sort '.$search->getSort());
        }

        $handler = $this->services[$search->getSort()];

        $paginator = $handler->search($search);

        $search->setTotal($paginator->count());
        $search->setRecords($paginator->getIterator()->getArrayCopy());

        return true;
    }
}