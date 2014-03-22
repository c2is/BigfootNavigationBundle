<?php

namespace Bigfoot\Bundle\NavigationBundle\Form\Type;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use BeSimple\I18nRoutingBundle\Routing\Router;

class LinkType extends AbstractType
{
    /**
     * @var Router
     */
    private $router;

    /**
     * Construct Item Type
     *
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $routes  = $this->router->getRouteCollection();
        $nRoutes = array();

        foreach ($routes as $key => $route) {
            if (($dotPos = strpos($key, '.')) !== false) {
                $key = substr($key, 0, $dotPos);
            }

            $routeOptions = $route->getOptions();

            if (isset($routeOptions['label'])) {
                $nRoutes[$key] = $routeOptions['label'];
            }
        }
        asort($nRoutes);

        $formModifier = function(FormInterface $form, $link) {
            if ($link) {
                $form->add(
                    'parameters',
                    'admin_route_parameter',
                    array(
                        'link' => $link,
                    )
                );
            }
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function(FormEvent $event) use ($formModifier, $nRoutes) {
                $form         = $event->getForm();
                $data         = $event->getData();
                $parentData   = $form->getParent()->getData();
                $getMethod    = 'get'.ucfirst($form->getName());
                $entityLink   = ($data) ? $parentData->$getMethod() : null;
                $name         = (isset($entityLink['name'])) ? $entityLink['name'] : null;
                $externalLink = (isset($entityLink['externalLink'])) ? $entityLink['externalLink'] : null;
                $linkType     = (isset($entityLink['linkType'])) ? $entityLink['linkType'] : true;

                $form->add(
                    'name',
                    'choice',
                    array(
                        'data'        => $name,
                        'empty_value' => 'Choose a link',
                        'choices'     => $nRoutes,
                        'required'    => false,
                        'attr'        => array(
                            'class' => 'admin_link_routes',
                        )
                    )
                );

                $form->add(
                    'externalLink',
                    'text',
                    array(
                        'data'     => $externalLink,
                        'required' => false,
                    )
                );

                $form->add(
                    'linkType',
                    'hidden',
                    array(
                        'data'     => $linkType,
                        'required' => false,
                    )
                );

                if (isset($data['name'])) {
                    $formModifier($event->getForm(), $data['name']);
                }
            });

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function(FormEvent $event) use ($formModifier) {
                $form       = $event->getForm();
                $data       = $event->getData();
                $parentData = $form->getParent()->getData();
                $setMethod  = 'set'.ucfirst($form->getName());

                $parentData->$setMethod($data);

                if (isset($data['name'])) {
                    $formModifier($event->getForm(), $data['name']);
                }
            });
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'admin_link';
    }
}
