<?php

namespace AppBundle\Entity;

use Alsciende\SerializerBundle\Annotation\Skizzle;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * Description of ClanRole
 *
 * @ORM\Entity()
 * @ORM\Table(name="clan_roles")
 *
 * @Skizzle()
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class ClanRole
{
    use TimestampableEntity;

    /**
     * @var Card
     *
     * @ORM\ManyToOne(targetEntity="Card")
     * @ORM\JoinColumn(name="card_id", referencedColumnName="id", nullable=false)
     *
     * @Skizzle\Field(type="association")
     */
    private $card;

    /**
     * @var string
     *
     * @ORM\Column(name="clan", type="string", nullable=false, unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     *
     * @Skizzle\Field(type="string")
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