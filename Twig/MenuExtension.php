<?php

namespace Bigfoot\Bundle\NavigationBundle\Twig;

use Bigfoot\Bundle\NavigationBundle\Entity\Menu\Item;
use Bigfoot\Bundle\NavigationBundle\Manager\Menu\Item\UrlManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class MenuExtension
 * @package Bigfoot\Bundle\NavigationBundle\Twig
 */
class MenuExtension extends \Twig_Extension
{
    /**
     * @var \Bigfoot\Bundle\NavigationBundle\Manager\Menu\Item\UrlManager
     */
    private $urlManager;

    /**
     * @param UrlManager $urlManager
     */
    public function __construct(UrlManager $urlManager)
    {
        $this->urlManager = $urlManager;
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
        return $this->urlManager->getUrl($item);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'bigfoot_navigation_menu';
    }
}
