<?php

namespace Bigfoot\Bundle\NavigationBundle\Form;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ItemParameterType extends AbstractType
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
                    if ($property = $data->getLabelField()) {
                        $valueField = $data->getValueField();
                        $results =  $this->entityManager->getRepository($data->getType())->createQueryBuilder('v')
                            ->select(sprintf('v.%s, v.%s',$valueField , $property))
                            ->orderBy(sprintf('v.%s', $property), 'ASC')
                            ->getQuery()->getArrayResult();

                        $choices = array();
                        foreach ($results as $result) {
                            $choices[$result[$valueField]] = $result[$property];
                        }
                    }


                    $form->add('value', 'choice', array(
                        'choices' => $choices,
                    ));
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
