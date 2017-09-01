<?php

namespace Bigfoot\Bundle\NavigationBundle\Form\Type\Route;

use BeSimple\I18nRoutingBundle\Routing\Router;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class ParameterType
 *
 * @package Bigfoot\Bundle\NavigationBundle\Form\Type\Route
 */
class ParameterType extends AbstractType
{
    /** @var \Doctrine\ORM\EntityManager */
    private $entityManager;

    /** @var \Symfony\Component\Routing\RouterInterface */
    private $router;

    /** @var string */
    private $locale;

    /** @var \Symfony\Component\PropertyAccess\PropertyAccessor */
    private $propertyAccessor;

    /**
     * ParameterType constructor.
     *
     * @param \Doctrine\ORM\EntityManager                        $entityManager
     * @param \Symfony\Component\Routing\RouterInterface         $router
     * @param string                                             $locale
     * @param \Symfony\Component\PropertyAccess\PropertyAccessor $propertyAccessor
     */
    public function __construct(
        EntityManager $entityManager,
        RouterInterface $router,
        $locale,
        PropertyAccessor $propertyAccessor
    ) {
        $this->entityManager    = $entityManager;
        $this->router           = $router;
        $this->locale           = $locale;
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $link = $options['link'];

        if ($link) {
            $routeName = $this->router instanceof Router ? sprintf('%s.%s', $link, $this->locale) : $link;
            $route     = $this->router->getRouteCollection()->get($routeName);

            if ($route) {
                $routeOptions = $route->getOptions();
            }
        }

        $entities   = [];
        $parameters = [];

        if (isset($routeOptions['parameters'])) {
            foreach ($routeOptions['parameters'] as $key => $parameter) {
                if (isset($parameter['type']) && preg_match('/Bundle/i', $parameter['type'])) {
                    $method = 'findBy';

                    if (isset($parameter['repoMethod'])) {
                        $method = $parameter['repoMethod'];
                    }

                    $methodParameters = [];

                    if (isset($parameter['label'])) {
                        $methodParameters[$parameter['label']] = 'ASC';
                    }

                    $entities[$parameter['name']] = $this->entityManager->getRepository($parameter['type'])->$method(
                        [],
                        $methodParameters
                    );
                } else {
                    $parameters[$parameter['name']] = isset($parameter['fieldLabel']) ? $parameter['fieldLabel'] : $parameter['name'];
                }
            }
        }

        if (count($entities)) {
            foreach ($entities as $key => $entity) {
                $builder->add(
                    $key,
                    ChoiceType::class,
                    [
                        'choices' => array_flip($this->getEntities($entity, $routeOptions)),
                    ]
                );
            }
        }

        if (count($parameters)) {
            foreach ($parameters as $key => $parameter) {
                $builder->add(
                    $key,
                    TextType::class,
                    [
                        'required' => true,
                        'label'    => $parameter,
                    ]
                );
            }
        }
    }

    /**
     * @param array $entities
     * @param array $options
     *
     * @return array
     */
    public function getEntities($entities = [], $options = [])
    {
        $nEntities = [];

        foreach ($entities as $key => $entity) {
            if (isset($options['property']) && $options['property']) {
                $label = $this->propertyAccessor->getValue($entity, $options['property']);
            } else {
                $label = (string)$entity;
            }

            $nEntities[$entity->getId()] = $label;
        }

        return $nEntities;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'link' => null,
            ]
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'bigfoot_route_parameter';
    }
}
