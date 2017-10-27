<?php
/**
 * Created by PhpStorm.
 * User: cedric
 * Date: 27/10/17
 * Time: 14:38
 */

namespace AppBundle\Behavior\Entity;


trait FreshnessTrait
{
    /**
     * @var int
     *
     * @ORM\Column(name="freshness", type="integer", nullable=false)
     */
    private $freshness;

    public function getFreshness (): int
    {
        return $this->freshness;
    }

    public function setFreshness (int $freshness): self
    {
        $this->freshness = $freshness;

        return $this;
    }
}
