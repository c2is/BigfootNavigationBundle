<?php

namespace Bigfoot\Bundle\NavigationBundle\Form\Type\Menu\Item;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Bigfoot\Bundle\NavigationBundle\Entity\Menu\Item\Parameter as ItemParameter;

class ParameterType extends AbstractType
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * Construct ParameterType
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
        $parameter = $options['data'];

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $itemParameter = new ItemParameter();

                $event->setData($itemParameter);
            }
        );

        $builder
            ->add(
                'id',
                'hidden',
                array(
                    'data'      => $parameter->getId(),
                    'read_only' => true,
                    'mapped'    => false,
                )
            )
            ->add(
                'name',
                'text',
                array(
                    'data'      => $parameter->getName(),
                    'read_only' => true,
                    'mapped'    => false,
                )
            )
            ->add('value', 'text', array('required' => false));

        if ($parameter->getType()) {
            $builder->add(
                'value',
                'choice',
                array(
                    'choices'  => $this->findAllValues($parameter),
                    'required' => true,
                )
            );
        }
    }

    /**
     * @return array
     */
    public function findAllValues($parameter)
    {
        $type       = $parameter->getType();
        $valueField = $parameter->getValueField();
        $labelField = $parameter->getLabelField();

        $results = $this->entityManager
            ->getRepository($type)
            ->createQueryBuilder('v')
            ->select(sprintf('v.id, v.%s, v.%s', $valueField, $labelField))
            ->orderBy(sprintf('v.%s', $labelField), 'ASC')
            ->getQuery()
            ->getArrayResult();

        $choices = array();

        foreach ($results as $result) {
            if (isset($result['id'])) {
                $choices[$result['id']] = $result[$labelField];
            }
        }

        return $choices;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Bigfoot\Bundle\NavigationBundle\Entity\Menu\Item\Parameter'
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'bigfoot_menu_item_parameter';
    }
}
