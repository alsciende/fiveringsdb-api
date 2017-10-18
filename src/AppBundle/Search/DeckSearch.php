<?php

namespace AppBundle\Search;

use AppBundle\Behavior\Entity\Timestampable;


/**
 * Description of DeckSearch
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DeckSearch extends AbstractPaginatedSearch implements Timestampable
{
    /**
     * @var string
     */
    private $sort;

    public function getSort(): ?string
    {
        return $this->sort;
    }

    public function setSort(string $sort): self
    {
        $this->sort = $sort;

        return $this;
    }

    public function getUpdatedAt()
    {
        return array_reduce(
            $this->getRecords(), function ($carry, Timestampable $item) {
            if ($carry && $item->getUpdatedAt() < $carry) {
                return $carry;
            } else {
                return $item->getUpdatedAt();
            }
        }
        );
    }


}