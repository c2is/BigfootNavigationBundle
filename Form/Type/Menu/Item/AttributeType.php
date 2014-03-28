<?php

namespace Bigfoot\Bundle\NavigationBundle\Form\Type\Menu\Item;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Bigfoot\Bundle\NavigationBundle\Entity\Menu\Item\Attribute;

class AttributeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'type',
                'choice',
                array(
                    'choices'     => Attribute::$types,
                    'multiple'    => false,
                    'empty_value' => 'Choose a type',
                    'required'    => true,
                )
            )
            ->add('name', 'text', array('required' => false))
            ->add('value', 'text', array('required' => false))
            ->add('label', 'text', array('required' => false));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Bigfoot\Bundle\NavigationBundle\Entity\Menu\Item\Attribute'
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'bigfoot_menu_item_attribute';
    }
}
