<?php

namespace AppBundle\Model;

use AppBundle\Entity\Deck;

/**
 * Description of DeckSearch
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DeckSearch
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
    private $pagesize;

    /**
     * Total number of results without pagination
     *
     * @var integer
     */
    private $total;

    /**
     * @var string
     */
    private $sort;

    /**
     * Current page of results
     *
     * @var Deck[]
     */
    private $decks;

    public function __construct ()
    {
        $this->pagesize = 30;
        $this->page = 1;
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

    public function getPagesize (): int
    {
        return $this->pagesize;
    }

    public function setPagesize (int $pagesize): self
    {
        $this->pagesize = $pagesize;

        return $this;
    }

    public function getTotal (): int
    {
        return $this->total;
    }

    public function setTotal (int $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getSort (): ?string
    {
        return $this->sort;
    }

    public function setSort (string $sort): self
    {
        $this->sort = $sort;

        return $this;
    }

    public function getDecks (): array
    {
        return $this->decks;
    }

    public function setDecks (array $decks): self
    {
        $this->decks = $decks;

        return $this;
    }
}