<?php

namespace Bigfoot\Bundle\NavigationBundle\Form\Type;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ParametersCollectionType extends AbstractType
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'parameters_collection';
    }

    public function getParent()
    {
        return 'collection';
    }
}
