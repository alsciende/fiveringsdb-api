<?php

namespace AppBundle\Search;

/**
 * Description of PaginatedSearchInterface
 *
 * @author Alsciende <alsciende@icloud.com>
 */
interface PaginatedSearchInterface extends SearchInterface
{
    public function getPage (): ?int;

    public function getLimit (): ?int;

    public function getFirstIndex (): int;
}
