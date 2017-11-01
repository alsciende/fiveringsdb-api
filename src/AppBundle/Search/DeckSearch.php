<?php

namespace AppBundle\Search;

/**
 * Description of DeckSearch
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DeckSearch extends AbstractPaginatedSearch
{
    /**
     * @var string
     */
    private $sort;

    public function getSort (): ?string
    {
        return $this->sort;
    }

    public function setSort (string $sort): self
    {
        $this->sort = $sort;

        return $this;
    }
}