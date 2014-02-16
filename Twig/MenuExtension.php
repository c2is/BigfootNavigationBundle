<?php

namespace Bigfoot\Bundle\NavigationBundle\Twig;

use Bigfoot\Bundle\NavigationBundle\Entity\Menu\Item;
use Bigfoot\Bundle\NavigationBundle\Entity\Menu\Item\Parameter;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class MenuExtension
 * @package Bigfoot\Bundle\NavigationBundle\Twig
 */
class MenuExtension extends \Twig_Extension
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager, RouterInterface $router)
    {
        $this->entityManager    = $entityManager;
        $this->router           = $router;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('menu_item_url', array($this, 'menuItemFunction'))
        );
    }

    /**
     * @param Item $item
     * @return string The generated URL for the $item menu item
     */
    public function menuItemFunction($item)
    {
        $url = '#';

        if ($externalLink = $item->getExternalLink()) {
            $url = ($httpPos = strpos($externalLink, 'http')) === false or $httpPos != 0 ? sprintf('http://%s', $externalLink) : $externalLink;
        } elseif ($route = $item->getRoute()) {
            $parameters = array();

            /**
             * @var Parameter $itemParameter
             */
            foreach ($item->getParameters() as $itemParameter) {
                $routeParameter = $itemParameter->getParameter();
                $parameterName = $routeParameter->getName();
                if ($routeParameter->getType()) {
                    $repository = $this->entityManager->getRepository($routeParameter->getType());
                    $entity = $repository->find($itemParameter->getValue());
                    $getter = sprintf('get%s', ucfirst($routeParameter->getValueField()));
                    $parameterValue = $entity->$getter();
                } else {
                    $parameterValue = $itemParameter->getValue();
                }

                $parameters[$parameterName] = $parameterValue;
            }

            $url = $this->router->generate($item->getRoute()->getName(), $parameters);
        }

        return $url;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'bigfoot_menu';
    }
}
