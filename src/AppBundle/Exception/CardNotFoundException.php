<?php

declare(strict_types=1);

namespace AppBundle\Exception;

use Throwable;

class CardNotFoundException extends \RuntimeException
{
    public function __construct ($card_code, $code = 0, Throwable $previous = null)
    {
        parent::__construct("Error: [$card_code]: no such card.", $code, $previous);
    }
}
