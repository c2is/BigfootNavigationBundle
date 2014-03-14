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
        $blank         = ($this->request->query->get('blank')) ?: false;
        $tpl           = ($this->request->query->get('tpl')) ?: false;
        $referer       = $this->request->headers->get('referer');
        $menuId        = substr($referer, (strrpos($referer, '/') + 1));

        if ($tpl && $menuId) {
            $menu = $this->entityManager->getRepository('BigfootNavigationBundle:Menu')->find($menuId);
            $options['data']->setMenu($menu);
        }

        if (!$blank || $tpl) {
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
