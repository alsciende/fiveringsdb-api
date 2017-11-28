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
    private $primaryClan;

    /**
     * @var string|null
     */
    private $secondaryClan;

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

    public function getPrimaryClan (): ?string
    {
        return $this->primaryClan;
    }

    public function setPrimaryClan ($primaryClan = null): self
    {
        $this->primaryClan = $primaryClan;

        return $this;
    }

    public function getSecondaryClan (): ?string
    {
        return $this->secondaryClan;
    }

    public function setSecondaryClan ($secondaryClan = null): self
    {
        $this->secondaryClan = $secondaryClan;

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