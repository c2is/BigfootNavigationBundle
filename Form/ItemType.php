<?php

namespace Bigfoot\Bundle\NavigationBundle\Form;

use Bigfoot\Bundle\CoreBundle\Route\RouteManager;
use Doctrine\ORM\EntityManager;
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
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * Constructor
     *
     * @param RouteManager $routeManager
     */
    public function __construct(RouteManager $routeManager, EntityManager $entityManager)
    {
        $this->routeManager = $routeManager;
        $this->entityManager = $entityManager;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $routes = $this->routeManager->getArrayRoutes();

        $builder
            ->add('route','choice', array(
                'choices' => $routes,
                'attr' => array(
                    'class' => 'menu-item-route-choice',
                ),
                'empty_value' => 'Choose a route',
                'required' => false,
            ))
            ->add('name')
            ->add('external_link','text',array('required' => false))
            ->add('attribute','text',array('required' => false))
            ->add('menu')
            ->add('parent')
            ->add('parameters', 'parameters_collection', array(
                'type'          => new ItemParameterType($this->entityManager),
                'prototype'     => false,
                'allow_add'     => true,
                'allow_delete'  => true,
                'label'         => 'Parameters',
                'by_reference'  => false,
            ))
            ->add('image','bigfoot_media', array('required' => false))
            ->add('translation', 'translatable_entity')
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
