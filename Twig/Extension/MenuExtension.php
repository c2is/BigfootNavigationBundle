<?php

namespace Bigfoot\Bundle\NavigationBundle\Twig\Extension;

use Symfony\Component\Routing\RouterInterface;
use Doctrine\ORM\EntityManager;
use Twig_Extension;
use Twig_Function_Method;
use Knp\Menu\Twig\Helper;
use Knp\Menu\Matcher\Matcher;
use Knp\Menu\Iterator\RecursiveItemIterator;
use Knp\Menu\Iterator\CurrentItemFilterIterator;
use Knp\Menu\Util\MenuManipulator;

use Bigfoot\Bundle\NavigationBundle\Entity\Menu\Item;
use Bigfoot\Bundle\NavigationBundle\Manager\Menu\Item\UrlManager;

/**
 * MenuExtension
 *
 * @package Bigfoot\Bundle\NavigationBundle\Twig
 */
class MenuExtension extends Twig_Extension
{
    /**
     * @var \Bigfoot\Bundle\NavigationBundle\Manager\Menu\Item\UrlManager
     */
    private $urlManager;

    private $helper;

    private $matcher;

    /**
     * Construct MenuExtension
     *
     * @param Helper     $helper
     * @param Matcher    $Matcher
     * @param UrlManager $urlManager
     */
    public function __construct(Helper $helper, Matcher $matcher, UrlManager $urlManager)
    {
        $this->helper     = $helper;
        $this->matcher    = $matcher;
        $this->urlManager = $urlManager;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'menu_item_url'         => new Twig_Function_Method($this, 'getItemUrl'),
            'knp_breadcrumb_render' => new Twig_Function_Method($this, 'getBreadcrumb', array('is_safe' => array('html')))
        );
    }

    /**
     * @param Item $item
     *
     * @return string The generated URL for the $item menu item
     */
    public function getItemUrl($item, $absolute = false)
    {
        return $this->urlManager->getUrl($item, $absolute);
    }

    /**
     * Renders a menu with the specified renderer.
     *
     * @param ItemInterface|string|array $menu
     *
     * @return string
     */
    public function getBreadcrumb($menu, $actions = null)
    {
        $menu = $this->helper->get($menu);

        $treeIterator = new \RecursiveIteratorIterator(
            new RecursiveItemIterator(
                new \ArrayIterator(array($menu))
            ),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        $iterator    = new CurrentItemFilterIterator($treeIterator, $this->matcher);
        $manipulator = new MenuManipulator();

        foreach ($iterator as $item) {
            return $manipulator->getBreadcrumbsArray($item);
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'admin_menu';
    }
}
