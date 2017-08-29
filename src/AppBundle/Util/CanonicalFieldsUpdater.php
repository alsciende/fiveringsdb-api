<?php

declare(strict_types=1);

namespace AppBundle\Util;

use AppBundle\Entity\Card;
use Cocur\Slugify\Slugify;

class CanonicalFieldsUpdater
{
    /** @var Slugify */
    private $slugify;

    public function __construct ()
    {
        $this->slugify = new Slugify(
            [
                'regexp' => '/([^[:ascii:]])+/',
            ]
        );
    }

    public function updateCanonicalFields (Card $card)
    {
        $card->setNameCanonical($this->slugify->slugify($card->getName()));
        if ($card->getText() !== null) {
            $card->setTextCanonical($this->slugify->slugify(strip_tags($card->getText())));
        }
    }
}
