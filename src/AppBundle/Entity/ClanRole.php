<?php

namespace AppBundle\Entity;

use Alsciende\SerializerBundle\Annotation\Source;
use AppBundle\Behavior\Entity\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * Description of ClanRole
 *
 * @ORM\Table(name="clan_roles")
 * @ORM\Entity
 *
 * @Source()
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class ClanRole implements Timestampable
{
    use TimestampableEntity;

    /**
     * @var Card
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Card")
     * @ORM\JoinColumn(name="card_id", referencedColumnName="id")
     *
     * @Source(type="association")
     */
    private $card;

    /**
     * @var string
     *
     * @ORM\Column(name="clan", type="string", nullable=false)
     *
     * @Source(type="string")
     */
    private $clan;

    function getCard (): Card
    {
        return $this->card;
    }

    function setCard (Card $card): self
    {
        $this->card = $card;

        return $this;
    }

    public function getClan (): string
    {
        return $this->clan;
    }

    public function setClan (string $clan): self
    {
        $this->clan = $clan;

        return $this;
    }
}