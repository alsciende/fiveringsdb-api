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

    public function __construct ()
    {
        $this->featured = false;
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

    public function getClan ()
    {
        return $this->clan;
    }

    public function setClan ($clan): self
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
}