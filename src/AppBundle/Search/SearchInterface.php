<?php

namespace AppBundle\Search;

/**
 * Description of SearchInterface
 *
 * @author Alsciende <alsciende@icloud.com>
 */
interface SearchInterface
{
    public function getTotal (): ?int;

    public function getRecords (): array;
}