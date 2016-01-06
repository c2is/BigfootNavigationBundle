<?php

namespace Bigfoot\Bundle\NavigationBundle\Form\Type\Route;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use BeSimple\I18nRoutingBundle\Routing\Router;

class ParameterType extends AbstractType
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var string
     */
    private $locale;

    /**
     * Construct Item Type
     *
     * @param Router $router
     */
    public function __construct(EntityManager $entityManager, Router $router, $locale)
    {
        $this->entityManager = $entityManager;
        $this->router        = $router;
        $this->locale        = $locale;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $link = $options['link'];

        if ($link) {
            $routeName = $this->router instanceof \BeSimple\I18nRoutingBundle\Routing\Router ? sprintf('%s.%s', $link, $this->locale) : $link;
            $route = $this->router->getRouteCollection()->get($routeName);

            if ($route) {
                $routeOptions = $route->getOptions();
            }
        }

        $entities   = array();
        $parameters = array();

        if (isset($routeOptions['parameters'])) {
            foreach ($routeOptions['parameters'] as $key => $parameter) {
                if (preg_match('/Bundle/i', $parameter['type'])) {
                    $method = 'findBy';

                    if (isset($parameter['repoMethod'])) {
                        $method = $parameter['repoMethod'];
                    }

                    $methodParameters = array();

                    if (isset($parameter['label'])) {
                        $methodParameters[$parameter['label']] = 'ASC';
                    }

                    $entities[$parameter['name']] = $this->entityManager->getRepository($parameter['type'])->$method(array(), $methodParameters);
                } else {
                    $parameters[$parameter['name']] = $parameter['name'];
                }
            }
        }

        if (count($entities)) {
            foreach ($entities as $key => $entity) {
                $builder->add(
                    $key,
                    'choice',
                    array(
                        'choices' => $this->getEntities($entity, $routeOptions),
                    )
                );
            }
        }

        if (count($parameters)) {
            foreach ($parameters as $key => $parameter) {
                $builder->add(
                    $parameter,
                    'text',
                    array(
                        'required' => true,
                    )
                );
            }
        }
    }

    public function getEntities($entities)
    {
        $nEntities = array();

        foreach ($entities as $key => $entity) {
            $nEntities[$entity->getId()] = (string) $entity;
        }

        return $nEntities;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'link' => null,
            )
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
