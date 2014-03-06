<?php

namespace Bigfoot\Bundle\NavigationBundle\Listener;

use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Bigfoot\Bundle\CoreBundle\Event\MenuEvent;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Menu Listener
 */
class MenuListener implements EventSubscriberInterface
{
    /**
     * @var \Symfony\Component\Security\Core\SecurityContextInterface
     */
    private $security;

    /**
     * @param SecurityContextInterface $security
     */
    public function __construct(SecurityContextInterface $security)
    {
        $this->security = $security;
    }

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

        if ($this->security->isGranted('ROLE_ADMIN')) {
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
                    'label'  => 'Item',
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

            $navigationMenu->addChild(
                'menu_item_attribute',
                array(
                    'label'  => 'Attribute',
                    'route'  => 'admin_menu_item_attribute',
                    'extras' => array(
                        'routes' => array(
                            'admin_menu_item_attribute_new',
                            'admin_menu_item_attribute_edit'
                        )
                    ),
                    'linkAttributes' => array(
                        'icon' => 'double-angle-right',
                    )
                )
            );
        }
    }
}