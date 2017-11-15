<?php

namespace AppBundle\Search;

use AppBundle\Entity\Card;

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

    /**
     * @var \DateTime|null
     */
    private $since;

    /**
     * @var string|null
     */
    private $clan;

    /**
     * @var bool
     */
    private $featured;

    /**
     * @var Card[]
     */
    protected $cards;

    public function __construct ()
    {
        $this->featured = false;
        $this->cards = [];
        parent::__construct();
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

    public function getSince (): ?\DateTime
    {
        return $this->since;
    }

    public function setSince (\DateTime $since = null): self
    {
        $this->since = $since;

        return $this;
    }

    public function getClan (): ?string
    {
        return $this->clan;
    }

    public function setClan ($clan = null): self
    {
        $this->clan = $clan;

        return $this;
    }

    public function isFeatured (): bool
    {
        return $this->featured;
    }

    public function setFeatured (bool $featured): self
    {
        $this->featured = $featured;

        return $this;
    }

    /** @param Card[] $cards */
    public function setCards (array $cards): self
    {
        $this->cards = $cards;

        return $this;
    }

    /** @return Card[] */
    public function getCards (): array
    {
        return $this->cards;
    }
}