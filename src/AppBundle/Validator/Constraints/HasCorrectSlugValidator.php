<?php

namespace AppBundle\Validator\Constraints;

use AppBundle\Entity\Card;
use Cocur\Slugify\SlugifyInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 */
class HasCorrectSlugValidator extends ConstraintValidator
{
    /**
     * @var SlugifyInterface
     */
    private $slugify;

    public function __construct (SlugifyInterface $slugify)
    {
        $this->slugify = $slugify;
    }

    public function validate ($card, Constraint $constraint)
    {
        if ($card instanceof Card && $constraint instanceof HasCorrectSlug) {
            if ($this->slugify->slugify($card->getFullName()) !== $card->getId()) {
                $this->context->buildViolation($constraint->message)
                              ->setParameter('{{ card.name }}', $card->getName())
                              ->addViolation();
            }
        }
    }
}
