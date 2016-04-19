<?php

namespace Bigfoot\Bundle\NavigationBundle\Form\Type\Menu;

use Bigfoot\Bundle\CoreBundle\Form\Type\TranslatedEntityType;
use Bigfoot\Bundle\MediaBundle\Form\Type\BigfootMediaType;
use Bigfoot\Bundle\NavigationBundle\Entity\Menu\Item\AttributeRepository;
use Bigfoot\Bundle\NavigationBundle\Form\Type\LinkType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
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

    public function setRequest(RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();
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
                    EntityType::class,
                    array(
                        'class'         => 'Bigfoot\Bundle\NavigationBundle\Entity\Menu',
                        'contextualize' => true
                    )
                );
        }

        $builder
            ->add('name', TextType::class, array('required' => false))
            ->add('label', TextType::class, array('required' => false))
            ->add('parent')
            ->add('link', LinkType::class, array('required' => false))
//            ->add('attributes', null, array('required' => false))

            ->add('childAttributes', EntityType::class, array(
                'class'         => 'BigfootNavigationBundle:Menu\Item\Attribute',
                'query_builder' => function (AttributeRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.type = :type')
                        ->setParameter(":type", Attribute::CHILD);
                },
                'multiple'      => true,
                'required'      => false
            ))
            ->add('elementAttributes', EntityType::class, array(
                'class'         => 'BigfootNavigationBundle:Menu\Item\Attribute',
                'query_builder' => function (AttributeRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.type = :type')
                        ->setParameter(":type", Attribute::ELEMENT);
                },
                'multiple'      => true,
                'required'      => false
            ))
            ->add('linkAttributes', EntityType::class, array(
                'class'         => 'BigfootNavigationBundle:Menu\Item\Attribute',
                'query_builder' => function (AttributeRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.type = :type')
                        ->setParameter(":type", Attribute::LINK);
                },
                'multiple'      => true,
                'required'      => false
            ))
            ->add('image', BigfootMediaType::class, array('required' => false))
            ->add('description', TextType::class, array('required' => false))
            ->add('translation', TranslatedEntityType::class);

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($entityManager, $menu, $parent) {
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
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
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
