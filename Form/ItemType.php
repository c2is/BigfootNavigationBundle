<?php

namespace Bigfoot\Bundle\NavigationBundle\Form;

use Bigfoot\Bundle\CoreBundle\Route\RouteManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ItemType extends AbstractType
{
    /**
     * @var RouteManager
     */
    protected $routeManager;

    /**
     * Constructor
     *
     * @param RouteManager $routeManager
     */
    public function __construct(RouteManager $routeManager)
    {
        $this->routeManager = $routeManager;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $routes = $this->routeManager->getArrayRoutes();
        $routeManager = $this->routeManager;

        $builder
            ->add('route','choice', array(
                'choices' => $routes,
                'attr' => array(
                    'class' => 'menu-item-route-choice',
                ),
                'empty_value' => 'Choose a route',
            ))
            ->add('name')
            ->add('menu')
            ->add('parent')
            ->add('parameters', 'collection', array(
                'type'          => new ItemParameterType(),
                'prototype'     => true,
                'allow_add'     => true,
                'allow_delete'  => true,
                'label'         => 'Parameters'
            ));
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Bigfoot\Bundle\NavigationBundle\Entity\Item'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'bigfoot_menu_item';
    }
}
