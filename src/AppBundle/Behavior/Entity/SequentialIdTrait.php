<?php

namespace AppBundle\Behavior\Entity;

/**
 * Description of SequentialIdTrait
 *
 * @author Alsciende <alsciende@icloud.com>
 */
trait SequentialIdTrait
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    public function getId (): int
    {
        return $this->id;
    }
}