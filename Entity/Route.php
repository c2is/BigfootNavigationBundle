<?php

namespace Bigfoot\Bundle\NavigationBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

use Bigfoot\Bundle\NavigationBundle\Entity\Route\Parameter;
use Bigfoot\Bundle\NavigationBundle\Entity\Menu\Item;

/**
 * Route
 *
 * @ORM\Table(name="bigfoot_route")
 * @ORM\Entity(repositoryClass="Bigfoot\Bundle\NavigationBundle\Entity\RouteRepository")
 */
class Route
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="label", type="string", length=255)
     */
    private $label;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Bigfoot\Bundle\NavigationBundle\Entity\Menu\Item", mappedBy="route", cascade={"persist"})
     */
    private $items;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Bigfoot\Bundle\NavigationBundle\Entity\Route\Parameter", mappedBy="route", cascade={"persist"})
     */
    private $parameters;

    /**
     * Construct Route
     */
    public function __construct()
    {
        $this->items      = new ArrayCollection();
        $this->parameters = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getLabel();
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
     * Set label
     *
     * @param string $label
     * @return Menu
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
     * Add items
     *
     * @param  $items
     * @return Route
     */
    public function addItem( $items)
    {
        $this->items[] = $items;

        return $this;
    }

    /**
     * Remove items
     *
     * @param  $items
     */
    public function removeItem( $items)
    {
        $this->items->removeElement($items);
    }

    /**
     * Get items
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Add parameters
     *
     * @param Parameter $parameters
     * @return Route
     */
    public function addParameter(Parameter $parameters)
    {
        $this->parameters[] = $parameters;

        return $this;
    }

    /**
     * Remove parameters
     *
     * @param Parameter $parameters
     */
    public function removeParameter(Parameter $parameters)
    {
        $this->parameters->removeElement($parameters);
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
     * Get parameters as array
     *
     * @return array
     */
    public function getParametersAsArray()
    {
        $parameters = array();

        foreach ($this->parameters as $key => $parameter) {
            $parameters[$key]['name']  = $parameter->getName();
            $parameters[$key]['type']  = $parameter->getType();
            $parameters[$key]['label'] = $parameter->getLabelField();
            $parameters[]['value'] = $parameter->getValueField();
        }

        return $parameters;
    }
}