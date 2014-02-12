<?php

namespace Bigfoot\Bundle\NavigationBundle\Entity\Menu\Item;

use Doctrine\ORM\Mapping as ORM;

use Bigfoot\Bundle\NavigationBundle\Entity\Menu\Item;
use Bigfoot\Bundle\NavigationBundle\Entity\Route\Parameter as RouteParameter;

/**
 * Parameter
 *
 * @ORM\Table(name="bigfoot_menu_item_parameter")
 * @ORM\Entity(repositoryClass="Bigfoot\Bundle\NavigationBundle\Entity\Menu\Item\ParameterRepository")
 */
class Parameter
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
     * @ORM\ManyToOne(targetEntity="Bigfoot\Bundle\NavigationBundle\Entity\Menu\Item", inversedBy="parameters")
     */
    private $item;

    /**
     * @ORM\ManyToOne(targetEntity="Bigfoot\Bundle\NavigationBundle\Entity\Route\Parameter", inversedBy="parameters")
     */
    private $parameter;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=255, nullable=true)
     */
    private $value;

    /**
     * toString Parameter
     *
     * @return string
     */
    public function __toString()
    {
        return $this->parameter->getValueField().':'.$this->value;
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
     * Set value
     *
     * @param string $value
     * @return Parameter
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
     * Set item
     *
     * @param Item $item
     * @return Parameter
     */
    public function setItem(Item $item = null)
    {
        $this->item = $item;

        return $this;
    }

    /**
     * Get item
     *
     * @return Item
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Set parameter
     *
     * @param RouteParameter $parameter
     * @return Parameter
     */
    public function setParameter(RouteParameter $parameter = null)
    {
        $this->parameter = $parameter;

        return $this;
    }

    /**
     * Get parameter
     *
     * @return RouteParameter
     */
    public function getParameter()
    {
        return $this->parameter;
    }
}