<?php
/**
 * Created by PhpStorm.
 * User: cedric
 * Date: 13/10/17
 * Time: 09:28
 */

namespace AppBundle\Validator\Constraints;

use AppBundle\Entity\Card;
use Cocur\Slugify\Slugify;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 */
class HasCorrectSlugValidator extends ConstraintValidator
{
    /** @var Slugify $slugify */
    private $slugify;

    public function __construct(Slugify $slugify)
    {
        $this->slugify = $slugify;
    }

    public function validate($card, Constraint $constraint)
    {
        if($card instanceof Card) {
            if($this->slugify->slugify($card->getName()) !== $card->getId()) {
                $this->context->buildViolation($constraint->message)
                              ->setParameter('{{ card.name }}', $card->getName())
                              ->addViolation();
            }
        }
    }
}
