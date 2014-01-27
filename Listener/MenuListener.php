<?php

namespace Bigfoot\Bundle\NavigationBundle\Listener;

use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Bigfoot\Bundle\CoreBundle\Event\MenuEvent;

/**
 * Menu Listener
 */
class MenuListener implements EventSubscriberInterface
{
    /**
     * Get subscribed events
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            MenuEvent::GENERATE_MAIN => 'onGenerateMain',
        );
    }

    /**
     * @param GenericEvent $event
     */
    public function onGenerateMain(GenericEvent $event)
    {
        $menu          = $event->getSubject();
        $structureMenu = $menu->getChild('structure');

        $navigationMenu = $structureMenu->addChild(
            'navigation_menu',
            array(
                'label'          => 'Navigation',
                'url'            => '#',
                'linkAttributes' => array(
                    'class' => 'dropdown-toggle',
                    'icon'  => 'sitemap',
                )
            )
        );

        $navigationMenu->setChildrenAttributes(
            array(
                'class' => 'submenu',
            )
        );

        $navigationMenu->addChild(
            'menu',
            array(
                'label'  => 'Menu',
                'route'  => 'admin_menu',
                'extras' => array(
                    'routes' => array(
                        'admin_menu_new',
                        'admin_menu_edit'
                    )
                ),
                'linkAttributes' => array(
                    'icon' => 'double-angle-right',
                )
            )
        );

        $navigationMenu->addChild(
            'menu_item',
            array(
                'label'  => 'Menu item',
                'route'  => 'admin_menu_item',
                'extras' => array(
                    'routes' => array(
                        'admin_menu_item_new',
                        'admin_menu_item_edit'
                    )
                ),
                'linkAttributes' => array(
                    'icon' => 'double-angle-right',
                )
            )
        );
    }
}