<?php

namespace Bigfoot\Bundle\NavigationBundle\Form\Type\Route;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ParameterType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $route      = $options['data']['route'];
        $parameters = $route->getParameters();

        foreach ($parameters as $key => $parameter) {
            $builder
                ->add(
                    'parameter_'.$key,
                    'admin_menu_item_parameter',
                    array(
                        'data' => $parameter,
                    )
                );
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'admin_route_parameter';
    }
}
