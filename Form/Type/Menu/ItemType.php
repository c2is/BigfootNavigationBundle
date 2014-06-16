<?php

namespace Bigfoot\Bundle\NavigationBundle\Form\Type\Menu;

use Bigfoot\Bundle\NavigationBundle\Entity\Menu\Item\AttributeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityManager;
use Bigfoot\Bundle\NavigationBundle\Entity\Menu\Item\Attribute;

class ItemType extends AbstractType
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * @var Request
     */
    protected $request;

    /**
     * Construct Item Type
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function setRequest(Request $request = null)
    {
        $this->request = $request;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $entityManager = $this->entityManager;
        $modal         = ($this->request->query->get('layout') == '_modal') ? true : false;
        $referer       = $this->request->headers->get('referer');
        $parent        = $this->request->query->get('parent');
        $menuId        = substr($referer, (strrpos($referer, '/') + 1));
        $menu          = false;

        if ($modal and $menuId) {
            $menu = $entityManager->getRepository('BigfootNavigationBundle:Menu')->find($menuId);
        }

        if (!$modal) {
            $builder
                ->add(
                    'menu',
                    'entity',
                    array(
                        'class'         => 'Bigfoot\Bundle\NavigationBundle\Entity\Menu',
                        'contextualize' => true
                    )
                );
        }

        $builder
            ->add('name', 'text', array('required' => false))
            ->add('parent')
            ->add('link', 'bigfoot_link', array('required' => false))

//            ->add('attributes', null, array('required' => false))

            ->add('childAttributes', 'entity', array(
                    'class' => 'BigfootNavigationBundle:Menu\Item\Attribute',
                    'query_builder' => function(AttributeRepository $er) {
                            return $er->createQueryBuilder('u')
                                ->where('u.type = :type')
                                ->setParameter(":type", Attribute::CHILD);
                        },
                    "multiple" => true
                ))

            ->add('elementAttributes', 'entity', array(
                    'class' => 'BigfootNavigationBundle:Menu\Item\Attribute',
                    'query_builder' => function(AttributeRepository $er) {
                            return $er->createQueryBuilder('u')
                                ->where('u.type = :type')
                                ->setParameter(":type", Attribute::ELEMENT);
                        },
                    "multiple" => true
                ))

            ->add('linkAttributes', 'entity', array(
                    'class' => 'BigfootNavigationBundle:Menu\Item\Attribute',
                    'query_builder' => function(AttributeRepository $er) {
                            return $er->createQueryBuilder('u')
                                ->where('u.type = :type')
                                ->setParameter(":type", Attribute::LINK);
                        },
                    "multiple" => true
                ))

            ->add('image', 'bigfoot_media', array('required' => false))
            ->add('description', 'text', array('required' => false))
            ->add('translation', 'translatable_entity');

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function(FormEvent $event) use ($entityManager, $menu, $parent) {
                $form = $event->getForm();
                $data = $event->getData();

                if ($menu != false) {
                    $data->setMenu($menu);
                }

                if ($parent) {
                    $cParent = $entityManager->getRepository('BigfootNavigationBundle:Menu\Item')->find($parent);
                    $data->setParent($cParent);
                }
            });
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        // var_dump($resolver->getDefaultOptions());die();
        $resolver->setDefaults(
            array(
                'data_class' => 'Bigfoot\Bundle\NavigationBundle\Entity\Menu\Item',
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'bigfoot_menu_item';
    }
}
