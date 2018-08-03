<?php

namespace AppBundle\Entity;

use Alsciende\SerializerBundle\Annotation\Skizzle;
use AppBundle\Model\CardSlotInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * Description of PackCard
 *
 * @ORM\Entity()
 * @ORM\Table(name="pack_cards")
 *
 * @Skizzle(break="pack_id")
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
     * @Skizzle\Field(type="integer")
     */
    private $quantity;

    /**
     * @var string
     *
     * @ORM\Column(name="position", type="string", nullable=false)
     *
     * @Skizzle\Field(type="string")
     */
    private $position;

    /**
     * @var string
     *
     * @ORM\Column(name="illustrator", type="string", nullable=true)
     *
     * @Skizzle\Field(type="string")
     */
    private $illustrator;

    /**
     * @var string|null
     *
     * @ORM\Column(name="flavor", type="string", nullable=true)
     *
     * @Skizzle\Field(type="string")
     */
    private $flavor;

    /**
     * @var string|null
     *
     * @ORM\Column(name="image_url", type="string", nullable=true)
     *
     * @Skizzle\Field(type="string")
     */
    private $imageUrl;

    /**
     * @var \AppBundle\Entity\Card
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Card", inversedBy="packCards", fetch="EAGER")
     * @ORM\JoinColumn(name="card_id", referencedColumnName="id", nullable=false)
     *
     * @Skizzle\Field(type="association")
     */
    private $card;

    /**
     * @var \AppBundle\Entity\Pack
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Pack", inversedBy="cards", fetch="EAGER")
     * @ORM\JoinColumn(name="pack_id", referencedColumnName="id", nullable=false)
     *
     * @Skizzle\Field(type="association")
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

    public function getFlavor (): ?string
    {
        return $this->flavor;
    }

    public function setFlavor (string $flavor = null): self
    {
        $this->flavor = $flavor;

        return $this;
    }

    public function getImageUrl (): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl (string $imageUrl = null): self
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }
}
