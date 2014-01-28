<?php

namespace Bigfoot\Bundle\NavigationBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityManager;

use Bigfoot\Bundle\NavigationBundle\Form\DataTransformer\ItemsToJsonTransformer;

/**
 * Menu Type
 */
class MenuType extends AbstractType
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * Construct MenuType
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
        $builder
            ->add('name')
            ->add(
                'items',
                'hidden',
                array(
                    'attr' => array(
                        'class'        => 'treeView',
                        'data-new-url' => '/admin_dev.php/admin/menu/item/new',
                    ),
                )
            )
            ->add('translation', 'translatable_entity');

        $builder
            ->get('items')
            ->addModelTransformer(new ItemsToJsonTransformer($this->entityManager));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Bigfoot\Bundle\NavigationBundle\Entity\Menu'
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'bigfoot_menu';
    }
}
