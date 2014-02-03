<?php

namespace Bigfoot\Bundle\NavigationBundle\Form\Type\Menu;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityManager;

use Bigfoot\Bundle\CoreBundle\Route\RouteManager;
use Bigfoot\Bundle\NavigationBundle\Form\Type\Menu\Item\ParameterType;

class ItemType extends AbstractType
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * @var RouteManager
     */
    protected $routeManager;

    /**
     * Construct Item Type
     *
     * @param EntityManager $entityManager
     * @param RouteManager  $routeManager
     */
    public function __construct(EntityManager $entityManager, RouteManager $routeManager)
    {
        $this->entityManager = $entityManager;
        $this->routeManager  = $routeManager;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $routes = $this->routeManager->getArrayRoutes();

        $builder
            ->add('menu')
            ->add('parent')
            ->add('name', 'text', array('required' => true))
            ->add(
                'linkType',
                'checkbox',
                array(
                    'label'    => 'External link',
                    'value'    => 0,
                    'mapped'   => false,
                    'required' => false
                )
            )
            ->add(
                'route',
                'choice',
                array(
                    'choices'     => $routes,
                    'label'       => 'Link category',
                    'empty_value' => 'Choose a route',
                    'required'    => false,
                    'attr'        => array(
                        'class' => 'menu-item-route-choice',
                ),
            ))
            ->add('externalLink', 'text', array('required' => false))
            ->add(
                'parameters',
                'bigfoot_menu_item_parameter_collection',
                array(
                    'type'         => 'bigfoot_menu_item_parameter',
                    'prototype'    => false,
                    'allow_add'    => true,
                    'allow_delete' => true,
                    'label'        => '',
                    'by_reference' => false,
                )
            )
            ->add('attributes')
            ->add('image', 'bigfoot_media', array('required' => false))
            ->add('description', 'text', array('required' => true))
            ->add('translation', 'translatable_entity');
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Bigfoot\Bundle\NavigationBundle\Entity\Menu\Item'
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'bigfoot_menu_item';
    }
}
