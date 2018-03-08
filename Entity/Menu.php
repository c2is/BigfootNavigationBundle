<?php

namespace Bigfoot\Bundle\NavigationBundle\Entity;

use Bigfoot\Bundle\NavigationBundle\Entity\Translation\MenuTranslation;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

use Bigfoot\Bundle\NavigationBundle\Entity\Menu\Item;

/**
 * Menu
 *
 * @Gedmo\TranslationEntity(class="Bigfoot\Bundle\NavigationBundle\Entity\Translation\MenuTranslation")
 * @ORM\Table(name="bigfoot_menu")
 * @ORM\Entity(repositoryClass="Bigfoot\Bundle\NavigationBundle\Entity\MenuRepository")
 */
class Menu
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
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
     * @Gedmo\Slug(fields={"name"}, updatable=false, unique=true)
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     */
    private $slug;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Bigfoot\Bundle\NavigationBundle\Entity\Menu\Item", mappedBy="menu", cascade={"persist", "merge", "remove"})
     */
    private $items;

    /**
     * @var string
     *
     * @Gedmo\Locale
     */
    private $locale;

    /**
     * @ORM\OneToMany(
     *   targetEntity="Bigfoot\Bundle\NavigationBundle\Entity\Translation\MenuTranslation",
     *   mappedBy="object",
     *   cascade={"persist", "remove"}
     * )
     */
    private $translations;

    /**
     * Construct Menu
     */
    public function __construct()
    {
        $this->translations = new ArrayCollection();
        $this->items        = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName() ?: '';
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
     * @return Menu
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
     * Set slug
     *
     * @param string $slug
     * @return Menu
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
     * @param ArrayCollection $items
     * @return $this
     */
    public function setItems(ArrayCollection $items = null)
    {
        $this->items = $items;

        foreach ($items as $item) {
            $item->setMenu($this);
        }

        return $this;
    }

    /**
     * @param Item $item
     * @return $this
     */
    public function addItem(Item $item)
    {
        $this->items->add($item);
        $item->setMenu($this);

        return $this;
    }

    /**
     * @param Item $item
     * @return $this
     */
    public function removeItem(Item $item)
    {
        $this->items->removeElement($item);
        $item->setMenu(null);

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getItems()
    {
        $items = array();

        foreach ($this->items as $item) {
            $items[$item->getPosition()] = $item;
        }

        ksort($items);

        return $items;
    }

    /**
     * @return ArrayCollection
     */
    public function getLvl1Items()
    {
        $items = array();

        foreach ($this->items as $item) {
            if (!$item->getParent()) {
                $items[$item->getPosition()] = $item;
            }
        }

        ksort($items);

        return $items;
    }

    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * @param MenuTranslation $t
     */
    public function addTranslation(MenuTranslation $t)
    {
        if (!$this->translations->contains($t)) {
            $this->translations[] = $t;
            $t->setObject($this);
        }
    }
}
