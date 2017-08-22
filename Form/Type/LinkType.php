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
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Routing\RouterInterface;

class LinkType extends AbstractType
{
    /** @var \Symfony\Component\Routing\RouterInterface */
    private $router;

    /** @var \Symfony\Component\PropertyAccess\PropertyAccessor */
    private $propertyAccessor;

    /**
     * LinkType constructor.
     *
     * @param \Symfony\Component\Routing\RouterInterface         $router
     * @param \Symfony\Component\PropertyAccess\PropertyAccessor $propertyAccessor
     */
    public function __construct(RouterInterface $router, PropertyAccessor $propertyAccessor)
    {
        $this->router           = $router;
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array                                        $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $routes  = $this->router->getRouteCollection();
        $nRoutes = [];

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

        $formModifier = function (FormInterface $form, $link) {
            if ($link) {
                $form->add(
                    'parameters',
                    ParameterType::class,
                    [
                        'link' => $link,
                    ]
                );
            }
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier, $nRoutes) {
                $form         = $event->getForm();
                $parentForm   = $event->getForm()->getParent();
                $data         = $event->getData();
                $parentData   = $form->getParent()->getData();
                $entityLink   = $data ? $this->propertyAccessor->getValue(
                    $parentData,
                    $this->getAccessorField($parentData, $form->getName())
                ) : null;
                $fieldName = $form->getName();
                $name         = (isset($entityLink['name'])) ? $entityLink['name'] : null;
                $externalLink = (isset($entityLink['externalLink'])) ? $entityLink['externalLink'] : null;
                $linkType     = (isset($entityLink['linkType'])) ? $entityLink['linkType'] : true;

                $form->add(
                    'name',
                    ChoiceType::class,
                    [
                        'data'        => $name,
                        'placeholder' => 'Choose a link',
                        'choices'     => array_flip($nRoutes),
                        'required'    => false,
                        'attr'        => [
                            'class'                  => 'bigfoot_link_routes',
                            'data-parent-form-link'  => get_class($parentForm->getConfig()->getType()->getInnerType()),
                            'data-parent-form-field' => $fieldName,
                        ],
                    ]
                );

                $form->add(
                    'externalLink',
                    TextType::class,
                    [
                        'data'     => $externalLink,
                        'required' => false,
                    ]
                );

                $form->add(
                    'linkType',
                    HiddenType::class,
                    [
                        'data'     => $linkType,
                        'required' => false,
                    ]
                );

                if (isset($data['name'])) {
                    $formModifier($event->getForm(), $data['name']);
                }
            }
        );

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $form       = $event->getForm();
                $data       = $event->getData();
                $parentData = $form->getParent()->getData();

                if ($parentData) {
                    $this->propertyAccessor->setValue(
                        $parentData,
                        $this->getAccessorField($parentData, $form->getName()),
                        $data
                    );

                    if (isset($data['name'])) {
                        $formModifier($event->getForm(), $data['name']);
                    }
                }
            }
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'bigfoot_link';
    }

    /**
     * @param array|object $data
     * @param string       $name
     *
     * @return string
     */
    private function getAccessorField($data, $name)
    {
        if (is_array($data)) {
            return '['.$name.']';
        }

        return $name;
    }
}
