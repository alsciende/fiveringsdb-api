<?php
/**
 * Created by PhpStorm.
 * User: cedric
 * Date: 13/10/17
 * Time: 09:26
 */

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class HasCorrectSlug extends Constraint
{
    public $message = 'The card "{{ card.name }}" has an incorrect slug id.';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
