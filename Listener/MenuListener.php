<?php

namespace Bigfoot\Bundle\NavigationBundle\Listener;

use Bigfoot\Bundle\CoreBundle\Event\MenuEvent;
use Bigfoot\Bundle\CoreBundle\Theme\Menu\Item;

/**
 * Class MenuListener
 * @package Bigfoot\Bundle\NavigationBundle\Listener
 */
class MenuListener
{
    /**
     * @param MenuEvent $event
     */
    public function onMenuGenerate(MenuEvent $event)
    {
        $menu = $event->getMenu();
        if ('sidebar_menu' == $menu->getName()) {
            $navigation = new Item('sidebar_navigation', 'Navigation');
            $navigation->addChild(new Item('sidebar_navigation_menu', 'Menu management', 'admin_menu'));
            $navigation->addChild(new Item('sidebar_navigation_item', 'Menu item management', 'admin_menu_item'));
            $menu->addItem($navigation);
        }
    }
}
