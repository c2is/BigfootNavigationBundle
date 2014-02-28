<?php

namespace Bigfoot\Bundle\NavigationBundle\Entity\Menu;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

use Bigfoot\Bundle\NavigationBundle\Entity\Menu;
use Bigfoot\Bundle\NavigationBundle\Entity\Route;
use Bigfoot\Bundle\NavigationBundle\Entity\Menu\Item\Attribute;
use Bigfoot\Bundle\NavigationBundle\Entity\Menu\Item\Parameter;

/**
 * Item
 *
 * @ORM\Table(name="bigfoot_menu_item")
 * @ORM\Entity(repositoryClass="Bigfoot\Bundle\NavigationBundle\Entity\Menu\ItemRepository")
 */
class Item
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Gedmo\Translatable
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"name"}, updatable=true, unique=true)
     * @ORM\Column(name="slug", type="string", length=255)
     */
    private $slug;

    /**
     * @var Menu
     *
     * @Gedmo\SortableGroup
     * @ORM\ManyToOne(targetEntity="Bigfoot\Bundle\NavigationBundle\Entity\Menu", inversedBy="items")
     */
    private $menu;

    /**
     * @var Item
     *
     * @Gedmo\SortableGroup
     * @ORM\ManyToOne(targetEntity="Item", inversedBy="children")
     */
    private $parent;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Item", mappedBy="parent", cascade={"remove"})
     */
    private $children;

    /**
     * @var integer
     *
     * @Gedmo\SortablePosition
     * @ORM\Column(name="position", type="integer")
     */
    private $position;

    /**
     * @var string
     *
     * @Assert\Url()
     * @ORM\Column(name="external_link", type="string", length=255, nullable=true)
     */
    private $externalLink;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @var Route
     *
     * @ORM\ManyToOne(targetEntity="Bigfoot\Bundle\NavigationBundle\Entity\Route", inversedBy="items")
     */
    private $route;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Bigfoot\Bundle\NavigationBundle\Entity\Menu\Item\Attribute", inversedBy="items")
     * @ORM\JoinTable(name="bigfoot_menu_item_attribute_join")
     */
    private $attributes;

    /**
     * @var \Datetime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @var \Datetime $updated
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    protected $updated;

    /**
     * @Gedmo\Blameable(on="create")
     * @ORM\Column(name="created_by", type="string", nullable=true)
     */
    protected $createdBy;

    /**
     * @Gedmo\Blameable(on="update")
     * @ORM\Column(name="updated_by", type="string", nullable=true)
     */
    protected $updatedBy;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Bigfoot\Bundle\NavigationBundle\Entity\Menu\Item\Parameter", mappedBy="item", cascade={"remove"})
     */
    private $parameters;

    /**
     * @Gedmo\Locale
     */
    private $locale;

    /**
     * Construct Item
     */
    public function __construct()
    {
        $this->children   = new ArrayCollection();
        $this->attributes = new ArrayCollection();
        $this->parameters = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
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
     * Set name
     *
     * @param string $name
     * @return Item
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
     * Set description
     *
     * @param string $description
     * @return Item
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Item
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set menu
     *
     * @param integer $menu
     * @return Item
     */
    public function setMenu($menu)
    {
        $this->menu = $menu;

        return $this;
    }

    /**
     * Get menu
     *
     * @return integer
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * Set parent
     *
     * @param Item $parent
     * @return Item
     */
    public function setParent(Item $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return Item
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param ArrayCollection $children
     * @return $this
     */
    public function setChildren(ArrayCollection $children)
    {
        $this->children = $children;
        return $this;
    }

    /**
     * @param Item $children
     * @return $this
     */
    public function addChildren(Item $children)
    {
        $this->children->add($children);
        return $this;
    }

    /**
     * @param Item $children
     * @return $this
     */
    public function removeChildren(Item $children)
    {
        $this->children->removeElement($children);
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @return ArrayCollection
     */
    public function getOrderedChildren()
    {
        $items = array();

        foreach ($this->children as $item) {
            if (!$item->getParent() != $this->getId()) {
                $items[$item->getPosition()] = $item;
            }
        }

        ksort($items);

        return $items;
    }

    /**
     * @param $route
     * @return $this
     */
    public function setRoute($route = null)
    {
        $this->route = $route;
        return $this;
    }

    /**
     * @return Route
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @param $position
     * @return $this
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param ArrayCollection $attributes
     * @return $this
     */
    public function setAttributes(ArrayCollection $attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * @param Item $attribute
     * @return $this
     */
    public function addAttribute(Attribute $attribute)
    {
        $attribute->addItem($this);
        $this->attributes->add($attribute);
        return $this;
    }

    /**
     * @param Item $attribute
     * @return $this
     */
    public function removeAttribute(Attribute $attribute)
    {
        $this->attributes->removeElement($attribute);
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @return ArrayCollection
     */
    public function getAttributesByType($type)
    {
        $toReturn = new ArrayCollection();

        foreach ($this->attributes as $attribute) {
            if ($attribute->getType() == $type) {
                $toReturn->add($attribute);
            }
        }
        return $toReturn;
    }

    /**
     * @return ArrayCollection
     */
    public function getElementAttributes()
    {
        return $this->getAttributesByType(Attribute::ELEMENT);
    }

    /**
     * @return ArrayCollection
     */
    public function getLinkAttributes()
    {
        return $this->getAttributesByType(Attribute::LINK);
    }

    /**
     * @return ArrayCollection
     */
    public function getChildAttributes()
    {
        return $this->getAttributesByType(Attribute::CHILD);
    }

    /**
     * @param $type
     * @return array
     */
    public function getArrayAttributesByType($type) {
        $toReturn = array();

        foreach ($this->attributes as $attribute) {
            if ($attribute->getType() == $type) {
                if (!isset($toReturn[$attribute->getName()])) {
                    $toReturn[$attribute->getName()] = array();
                }
                $toReturn[$attribute->getName()][] = $attribute->getValue();
            }
        }

        return $toReturn;
    }

    /**
     * @param $type
     */
    public function getChildAttributeValueByName($name) {
        foreach ($this->attributes as $attribute) {
            if ($attribute->getType() == Attribute::CHILD && $attribute->getName() == $name) {
                return $attribute->getValue();
            }
        }

        return false;
    }

    public function attributeExist($name, $value) {
        foreach ($this->attributes as $attribute) {
            if ($attribute->getName() == $name && $attribute->getValue() == $value) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public function getElementArrayAttributes()
    {
        return $this->getArrayAttributesByType(Attribute::ELEMENT);
    }

    /**
     * @return array
     */
    public function getLinkArrayAttributes()
    {
        return $this->getArrayAttributesByType(Attribute::LINK);
    }

    /**
     * @return array
     */
    public function getChildArrayAttributes()
    {
        return $this->getArrayAttributesByType(Attribute::CHILD);
    }

    /**
     * Set externalLink
     *
     * @param string $externalLink
     * @return Item
     */
    public function setExternalLink($externalLink)
    {
        $this->externalLink = $externalLink;

        return $this;
    }

    /**
     * Get externalLink
     *
     * @return string
     */
    public function getExternalLink()
    {
        return $this->externalLink;
    }

    /**
     * Set image
     *
     * @param string $image
     * @return Page
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }


    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Item
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return Item
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set createdBy
     *
     * @param string $createdBy
     * @return Item
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return string
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set updatedBy
     *
     * @param string $updatedBy
     * @return Item
     */
    public function setUpdatedBy($updatedBy)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * Get updatedBy
     *
     * @return string
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * Add parameter
     *
     * @param Parameter $parameter
     * @return Item
     */
    public function addParameter($parameter)
    {
        $this->parameters[] = $parameter;

        return $this;
    }

    /**
     * Remove parameter
     *
     * @param Parameter $parameter
     */
    public function removeParameter(Parameter $parameter)
    {
        $this->parameters->removeElement($parameter);
    }

    /**
     * Get parameters
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param string $locale
     * @return $this
     */
    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }
}
