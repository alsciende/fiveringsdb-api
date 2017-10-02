<?php

namespace AppBundle\Search;

/**
 * Description of AbstractSearch
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class AbstractSearch implements SearchInterface
{
    /**
     * @var integer
     */
    private $total;

    /**
     * @var array
     */
    private $records;

    public function __construct ()
    {
        $this->records = [];
    }

    public function getTotal (): ?int
    {
        return $this->total;
    }

    public function setTotal (int $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getRecords (): array
    {
        return $this->records;
    }

    public function setRecords (array $records): self
    {
        $this->records = $records;

        return $this;
    }
}