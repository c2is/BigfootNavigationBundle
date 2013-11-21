<?php

namespace Bigfoot\Bundle\NavigationBundle\Form;

use Bigfoot\Bundle\NavigationBundle\Form\DataTransformer\ItemsToJsonTransformer;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class MenuType
 * @package Bigfoot\Bundle\NavigationBundle\Form
 */
class MenuType extends AbstractType
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * Constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new ItemsToJsonTransformer($this->entityManager);

        $builder
            ->add('name')
            ->add($builder->create('items', 'hidden', array(
                    'attr' => array(
                        'class' => 'treeView',
                        'data-new-url' => '/admin_dev.php/admin/menu/item/new',
                    ),
                ))
                ->addModelTransformer($transformer)
            )
            ->add('translation', 'translatable_entity')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Bigfoot\Bundle\NavigationBundle\Entity\Menu'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'bigfoot_menu';
    }
}
