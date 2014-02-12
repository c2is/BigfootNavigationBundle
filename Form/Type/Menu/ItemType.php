<?php

namespace Bigfoot\Bundle\NavigationBundle\Form\Type\Menu;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityManager;

use Bigfoot\Bundle\CoreBundle\Manager\RouteManager;
use Bigfoot\Bundle\NavigationBundle\Form\Type\Menu\Item\ParameterType;

class ItemType extends AbstractType
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * @var RouteManager
     */
    protected $routeManager;

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
    public function __construct(EntityManager $entityManager, RouteManager $routeManager)
    {
        $this->entityManager = $entityManager;
        $this->routeManager  = $routeManager;
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
        $routeManager  = $this->routeManager;
        $routes        = $routeManager->getArrayRoutes();
        $modal         = ($this->request->query->get('modal')) ?: false;
        $referer       = $this->request->headers->get('referer');
        $menuId        = substr($referer, (strrpos($referer, '/') + 1));

        if ($menuId) {
            $menu = $this->entityManager->getRepository('BigfootNavigationBundle:Menu')->find($menuId);
            $options['data']->setMenu($menu);
        }

        if (!$modal) {
            $builder
                ->add('menu')
                ->add('parent');
        }

        $builder
            ->add('name', 'text', array('required' => false))
            ->add(
                'linkType',
                'checkbox',
                array(
                    'label'    => 'External link',
                    'data'     => ($options['data']->getExternalLink()) ? true : false,
                    'mapped'   => false,
                    'required' => false
                )
            )
            ->add(
                'route',
                'entity',
                array(
                    'class'       => 'Bigfoot\Bundle\NavigationBundle\Entity\Route',
                    'label'       => 'Link category',
                    'empty_value' => 'Choose a route',
                    'required'    => false,
                    'attr'        => array(
                        'class' => 'menu-item-route-choice',
                    ),
                )
            )
            ->add('externalLink', 'text', array('required' => false));

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function(FormEvent $event) use ($entityManager) {
                $form  = $event->getForm();
                $route = $event->getData()->getRoute();

                if ($route) {
                    $dbRoute = $entityManager->getRepository('BigfootNavigationBundle:Route')->findOneByName($route->getName());

                    if ($dbRoute) {
                        $form->add(
                            'parameters',
                            'admin_route_parameter',
                            array(
                                'mapped' => false,
                                'data'   => array(
                                    'route'  => $dbRoute,
                                )
                            )
                        );
                    }
                }
            }
        );

        $builder
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
                'data_class' => 'Bigfoot\Bundle\NavigationBundle\Entity\Menu\Item'
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
