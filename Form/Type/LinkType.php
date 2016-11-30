<?php

namespace Bigfoot\Bundle\NavigationBundle\Form\Type;

use Bigfoot\Bundle\NavigationBundle\Form\Type\Route\ParameterType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Routing\RouterInterface;

class LinkType extends AbstractType
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * Construct Item Type
     *
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
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
                    ParameterType::class,
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
                $parentForm   = $event->getForm()->getParent();
                $data         = $event->getData();
                $parentData   = $form->getParent()->getData();
                $getMethod    = 'get'.ucfirst($form->getName());
                $entityLink   = ($data) ? $parentData->$getMethod() : null;
                $name         = (isset($entityLink['name'])) ? $entityLink['name'] : null;
                $externalLink = (isset($entityLink['externalLink'])) ? $entityLink['externalLink'] : null;
                $linkType     = (isset($entityLink['linkType'])) ? $entityLink['linkType'] : true;

                $form->add(
                    'name',
                    ChoiceType::class,
                    array(
                        'data'        => $name,
                        'placeholder' => 'Choose a link',
                        'choices'     => array_flip($nRoutes),
                        'required'    => false,
                        'attr'        => array(
                            'class'                 => 'bigfoot_link_routes',
                            'data-parent-form-link' => get_class($parentForm->getConfig()->getType()->getInnerType()),
                        )
                    )
                );

                $form->add(
                    'externalLink',
                    TextType::class,
                    array(
                        'data'     => $externalLink,
                        'required' => false,
                    )
                );

                $form->add(
                    'linkType',
                    HiddenType::class,
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

                if ($parentData) {
                    $parentData->$setMethod($data);

                    if (isset($data['name'])) {
                        $formModifier($event->getForm(), $data['name']);
                    }
                }
            });
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'bigfoot_link';
    }
}
