<?php

namespace Bigfoot\Bundle\NavigationBundle\Form\Type\Menu;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityManager;

use Bigfoot\Bundle\CoreBundle\Manager\RouteManager;
use Bigfoot\Bundle\NavigationBundle\Entity\Menu\Item;
use Bigfoot\Bundle\NavigationBundle\Form\Type\Menu\Item\ParameterType;

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
     * @param RouteManager  $routeManager
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
        $menuId        = substr($referer, (strrpos($referer, '/') + 1));
        $menu          = false;

        if ($menuId) {
            $menu = $this->entityManager->getRepository('BigfootNavigationBundle:Menu')->find($menuId);
        }

        if (!$modal) {
            $builder
                ->add('menu')
                ->add('parent');
        }

        $builder
            ->add('name', 'text', array('required' => false))
            ->add('link', 'admin_link', array('required' => false))
            ->add('attributes')
            ->add('image', 'bigfoot_media', array('required' => false))
            ->add('description', 'text', array('required' => false))
            ->add('translation', 'translatable_entity');

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function(FormEvent $event) use ($menu) {
                $form = $event->getForm();
                $data = $event->getData();

                if ($menu != false) {
                    $data->setMenu($menu);
                }
            });
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
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
        return 'admin_menu_item';
    }
}
