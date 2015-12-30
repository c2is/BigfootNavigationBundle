<?php

namespace Bigfoot\Bundle\NavigationBundle\Subscriber;

use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

use Bigfoot\Bundle\CoreBundle\Event\MenuEvent;

/**
 * Menu Subscriber
 */
class MenuSubscriber implements EventSubscriberInterface
{
    /**
     * @var TokenStorage
     */
    private $security;

    /**
     * @param TokenStorage $security
     */
    public function __construct(TokenStorage $security)
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
            MenuEvent::GENERATE_MAIN => array('onGenerateMain', 5)
        );
    }

    /**
     * @param GenericEvent $event
     */
    public function onGenerateMain(GenericEvent $event)
    {
        $builder = $event->getSubject();

        if (!$builder->childExists('structure')) {
            $builder
                ->addChild(
                    'structure',
                    array(
                        'label'          => 'Structure',
                        'url'            => '#',
                        'attributes' => array(
                            'class' => 'parent',
                        ),
                        'linkAttributes' => array(
                            'class' => 'dropdown-toggle',
                            'icon'  => 'building',
                        )
                    ),
                    array(
                        'children-attributes' => array(
                            'class' => 'submenu'
                        )
                    )
                );
        }

        $builder
            ->addChildFor(
                'structure',
                'structure_navigation',
                array(
                    'label'          => 'Navigation',
                    'url'            => '#',
                    'linkAttributes' => array(
                        'class' => 'dropdown-toggle',
                        'icon'  => 'sitemap',
                    )
                ),
                array(
                    'children-attributes' => array(
                        'class' => 'submenu'
                    )
                )
            )
            ->addChildFor(
                'structure_navigation',
                'structure_navigation_menu',
                array(
                    'label'  => 'Menu',
                    'route'  => 'bigfoot_menu',
                    'extras' => array(
                        'routes' => array(
                            'bigfoot_menu_new',
                            'bigfoot_menu_edit'
                        )
                    ),
                    'linkAttributes' => array(
                        'icon' => 'double-angle-right',
                    )
                )
            )
            ->addChildFor(
                'structure_navigation',
                'structure_navigation_item',
                array(
                    'label'  => 'Item',
                    'route'  => 'bigfoot_menu_item',
                    'extras' => array(
                        'routes' => array(
                            'bigfoot_menu_item_new',
                            'bigfoot_menu_item_edit'
                        )
                    ),
                    'linkAttributes' => array(
                        'icon' => 'double-angle-right',
                    )
                )
            )
            ->addChildFor(
                'structure_navigation',
                'structure_navigation_attribute',
                array(
                    'label'  => 'Attribute',
                    'route'  => 'bigfoot_menu_item_attribute',
                    'extras' => array(
                        'routes' => array(
                            'bigfoot_menu_item_attribute_new',
                            'bigfoot_menu_item_attribute_edit'
                        )
                    ),
                    'linkAttributes' => array(
                        'icon' => 'double-angle-right',
                    )
                )
            );
    }
}
