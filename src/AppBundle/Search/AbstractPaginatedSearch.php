<?php

namespace AppBundle\Search;

/**
 * Description of AbstractPaginatedSearch
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class AbstractPaginatedSearch extends AbstractSearch implements PaginatedSearchInterface
{
    /**
     * Current page
     *
     * @var integer
     */
    private $page;

    /**
     * Number of results per page
     *
     * @var integer
     */
    private $limit;

    public function __construct ()
    {
        $this->page = 1;
        $this->limit = 30;
        parent::__construct();
    }

    public function getPage (): ?int
    {
        return $this->page;
    }

    public function setPage (int $page): self
    {
        $this->page = $page;

        return $this;
    }

    public function getLimit (): int
    {
        return $this->limit;
    }

    public function setLimit (int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    public function getFirstIndex (): int
    {
        return ($this->page - 1) * $this->limit;
    }
}