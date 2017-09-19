<?php

namespace AppBundle\Entity;

use AppBundle\Model\CardSlotInterface;
use Doctrine\ORM\Mapping as ORM;
use Alsciende\SerializerBundle\Annotation\Source;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use JMS\Serializer\Annotation as JMS;

/**
 * Description of PackCard
 *
 * @ORM\Table(name="pack_cards")
 * @ORM\Entity
 *
 * @Source(break="pack_id")
 *
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessorOrder("alphabetical")
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class PackCard implements CardSlotInterface
{
    use TimestampableEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer", nullable=false)
     *
     * @Source(type="integer")
     *
     * @JMS\Expose
     */
    private $quantity;

    /**
     * @var string
     *
     * @ORM\Column(name="position", type="string", nullable=false)
     *
     * @Source(type="string")
     *
     * @JMS\Expose
     */
    private $position;

    /**
     * @var string
     *
     * @ORM\Column(name="illustrator", type="string", nullable=true)
     *
     * @Source(type="string")
     *
     * @JMS\Expose
     */
    private $illustrator;

    /**
     * @var string
     *
     * @ORM\Column(name="flavor", type="string", nullable=true)
     *
     * @Source(type="string")
     *
     * @JMS\Expose
     */
    private $flavor;

    /**
     * @var string
     *
     * @ORM\Column(name="image_url", type="string", nullable=true)
     *
     * @JMS\Expose
     */
    private $imageUrl;

    /**
     * @var \AppBundle\Entity\Card
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Card", inversedBy="packCards")
     * @ORM\JoinColumn(name="card_id", referencedColumnName="id")
     *
     * @Source(type="association")
     *
     * @JMS\Expose
     * @JMS\Groups({"card_group"})
     */
    private $card;

    /**
     * @var \AppBundle\Entity\Pack
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Pack", inversedBy="cards")
     * @ORM\JoinColumn(name="pack_id", referencedColumnName="id")
     *
     * @Source(type="association")
     *
     * @JMS\Expose
     * @JMS\Groups({"pack_group"})
     */
    private $pack;

    public function __toString ()
    {
        return sprintf('%s (#%s of %s)', $this->getCard()->getName(), $this->getPosition(), $this->getPack()->getName());
    }

    function getQuantity (): int
    {
        return $this->quantity;
    }

    function getCard (): Card
    {
        return $this->card;
    }

    function getPack (): Pack
    {
        return $this->pack;
    }

    function setQuantity ($quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    function setCard (Card $card): self
    {
        $this->card = $card;

        return $this;
    }

    function setPack (Pack $pack): self
    {
        $this->pack = $pack;

        return $this;
    }

    public function getPosition (): string
    {
        return $this->position;
    }

    public function setPosition (string $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getIllustrator (): string
    {
        return $this->illustrator;
    }

    public function setIllustrator (string $illustrator): self
    {
        $this->illustrator = $illustrator;

        return $this;
    }

    public function getFlavor (): string
    {
        return $this->flavor;
    }

    public function setFlavor (string $flavor): self
    {
        $this->flavor = $flavor;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(string $imageUrl = null): self
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }
}
