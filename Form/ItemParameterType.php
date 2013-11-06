<?php

namespace Bigfoot\Bundle\NavigationBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ItemParameterType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('parameter', 'text', array(
                'read_only' => true,
                'label'     => 'Name'
            ))
            ->add('value')
            ->add('type', 'hidden')
            ->add('labelField', 'hidden')
            ->add('valueField', 'hidden')
            ->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event) {
                $form = $event->getForm();
                $data = $event->getData();

                if (!$data) {
                    return null;
                }

                if ($data->getType()) {
                    $valueParameters = array('class' => $data->getType());
                    if ($property = $data->getLabelField()) {
                        $valueParameters['property'] = $property;
                        $valueParameters['query_builder'] = function(EntityRepository $er) use ($property) {
                            $queryBuilder =  $er->createQueryBuilder('v')
                                ->orderBy(sprintf('v.%s', $property), 'ASC');

                            return $queryBuilder;
                        };
                    }

                    $form->add('value', 'entity', $valueParameters);
                } else {
                    $form->add('value', 'text');
                }
            });
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Bigfoot\Bundle\NavigationBundle\Entity\ItemParameter'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'bigfoot_menu_item_parameter';
    }
}
