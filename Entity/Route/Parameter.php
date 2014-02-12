<?php

namespace Bigfoot\Bundle\NavigationBundle\Entity\Route;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use Bigfoot\Bundle\NavigationBundle\Entity\Route;
use Bigfoot\Bundle\NavigationBundle\Entity\Route\Parameter as ItemParameter;

/**
 * Parameter
 *
 * @ORM\Table(name="bigfoot_route_parameter")
 * @ORM\Entity(repositoryClass="Bigfoot\Bundle\NavigationBundle\Entity\Route\ParameterRepository")
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=255, nullable=true)
     */
    private $value;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="value_field", type="string", length=255, nullable=true)
     */
    private $valueField;

    /**
     * @var string
     *
     * @ORM\Column(name="label_field", type="string", length=255, nullable=true)
     */
    private $labelField;

    /**
     * @ORM\ManyToOne(targetEntity="Bigfoot\Bundle\NavigationBundle\Entity\Route", inversedBy="parameters", cascade={"persist"})
     */
    private $route;

    /**
     * @ORM\OneToMany(targetEntity="Bigfoot\Bundle\NavigationBundle\Entity\Menu\Item\Parameter", mappedBy="parameter")
     */
    private $parameters;

    /**
     * Construct Parameter
     */
    public function __construct()
    {
        $this->parameters = new ArrayCollection();
    }

    /**
     * toString Parameter
     *
     * @return string
     */
    public function __toString()
    {
        return $this->name;
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
     * @return Itemname
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
     * @return ItemParameter
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
     * Set type
     *
     * @param string $type
     * @return string
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set valueField
     *
     * @param string $type
     * @return string
     */
    public function setValueField($valueField)
    {
        $this->valueField = $valueField;

        return $this;
    }

    /**
     * Get valueField
     *
     * @return string
     */
    public function getValueField()
    {
        return $this->valueField;
    }

    /**
     * Set labelField
     *
     * @param string $type
     * @return string
     */
    public function setLabelField($labelField)
    {
        $this->labelField = $labelField;

        return $this;
    }

    /**
     * Get labelField
     *
     * @return string
     */
    public function getLabelField()
    {
        return $this->labelField;
    }

    /**
     * Set route
     *
     * @param Route $route
     * @return Parameter
     */
    public function setRoute(Route $route = null)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Get route
     *
     * @return Route
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Add parameters
     *
     * @param ItemParameter $parameters
     * @return Parameter
     */
    public function addParameter(ItemParameter $parameters)
    {
        $this->parameters[] = $parameters;

        return $this;
    }

    /**
     * Remove parameters
     *
     * @param ItemParameter $parameters
     */
    public function removeParameter(ItemParameter $parameters)
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
}