<?php

namespace Bigfoot\Bundle\NavigationBundle\Entity\Menu\Item;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

use Bigfoot\Bundle\NavigationBundle\Entity\Menu\Item;

/**
 * Attribute
 *
 * @ORM\Table(name="bigfoot_menu_item_attribute")
 * @ORM\Entity(repositoryClass="Bigfoot\Bundle\NavigationBundle\Entity\Menu\Item\AttributeRepository")
 */
class Attribute
{
    const ELEMENT = 1;
    const LINK    = 2;
    const CHILD   = 3;

    public static $types = array(
        self::ELEMENT => 'Element',
        self::LINK    => 'Link',
        self::CHILD   => 'Child'
    );

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="attr_type", type="smallint")
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=255)
     */
    private $value;

    /**
     * @var string
     * @Gedmo\Translatable
     * @ORM\Column(name="label", type="string", length=255)
     */
    private $label;

    /**
     * @ORM\ManyToMany(targetEntity="Bigfoot\Bundle\NavigationBundle\Entity\Menu\Item", mappedBy="attributes")
     */
    private $items;
    
    /**
     * @Gedmo\Locale
     */
    private $locale;

    /**
     * Construct Attribute
     */
    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    /**
     * toString Attribute
     *
     * @return string
     */
    public function __toString()
    {
        return $this->label;
    }

    /**
     * @param ArrayCollection $items
     * @return $this
     */
    public function setItems(ArrayCollection $items)
    {
        $this->items = $items;
        return $this;
    }

    /**
     * @param Item $item
     * @return $this
     */
    public function addItem(Item $item)
    {
        $this->items->add($item);
        return $this;
    }

    /**
     * @param Item $item
     * @return $this
     */
    public function removeItem(Item $item)
    {
        $this->items->removeElement($item);
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set type
     *
     * @param int $type
     * @return Attribute
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Attribute
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return Attribute
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set label
     *
     * @param string $label
     * @return Attribute
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }
    
    /**
     * @param $locale
     */
    public function setTranslatableLocale($locale)
    {
        return $this->locale = $locale;
    }
}
