<?php

namespace Bigfoot\Bundle\NavigationBundle\Manager\Menu\Item;

use Bigfoot\Bundle\ContextBundle\Service\ContextService as Context;
use Bigfoot\Bundle\NavigationBundle\Entity\Menu\Item;
use Bigfoot\Bundle\NavigationBundle\Entity\Menu\Item\Parameter;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;

class UrlManager
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;

    /** @var Context */
    private $context;

    /**
     * @param EntityManager   $entityManager
     * @param RouterInterface $router
     * @param Context         $context
     */
    public function __construct(EntityManager $entityManager, RouterInterface $router, Context $context)
    {
        $this->entityManager = $entityManager;
        $this->router        = $router;
        $this->context       = $context;
    }

    /**
     * @param Item $item
     * @param int $absolute
     *
     * @return string
     */
    public function getUrl(Item $item, $absolute = Router::ABSOLUTE_URL)
    {
        $link = $item->getLink();

        return $this->getLink($link, $absolute);
    }

    /**
     * @param array $link
     * @param int  $absolute
     *
     * @return string
     */
    public function getLink($link, $absolute = Router::ABSOLUTE_URL)
    {
        $url = '#';

        if (isset($link['name'])) {
            $parameters  = array();
            $locale = $this->context->get('language');
            $route       = $link['name'];

            if ($this->router instanceof \BeSimple\I18nRoutingBundle\Routing\Router and $this->router->getRouteCollection(
                )->get(sprintf('%s.%s', $route, $locale))) {
                $parameters['locale'] = $locale;
                $sfRoute              = $this->router->getRouteCollection()->get($route.'.'.$locale);
            } elseif ($this->router instanceof \JMS\I18nRoutingBundle\Router\I18nRouter and $this->router->getRouteCollection()->get(sprintf('%s__RG__%s', $locale, $route))){
                $sfRoute = $this->router->getRouteCollection()->get($locale.'__RG__'.$route);
            } else {
                $sfRoute = $this->router->getRouteCollection()->get($route);
            }

            if (!$sfRoute) {
                return $url;
            }

            $options     = $sfRoute->getOptions();
            $iParameters = isset($link['parameters']) ? $link['parameters'] : [];

            if (isset($options['parameters'])) {
                foreach ($options['parameters'] as $parameter) {
                    if (isset($parameter['name']) && isset($parameter['type']) && preg_match(
                            '/Bundle/i',
                            $parameter['type']
                        )) {
                        if (is_array($parameter['name'])) {
                            $mainParameter = $parameter['name'][0];
                            $parameterNames = $parameter['name'];
                        } else {
                            $field  = isset($parameter['field']) ? $parameter['field'] : $parameter['name'];
                            $mainParameter = $parameter['name'];
                            $parameterNames = [$field => $parameter['name']];
                        }

                        if (!$iParameters[$mainParameter]) {
                            return $url;
                        }

                        $entity = $this->entityManager->getRepository($parameter['type'])->find(
                            $iParameters[$mainParameter]
                        );

                        if (!$entity) {
                            return $url;
                        }

                        foreach ($parameterNames as $parameterField => $parameterName) {
                            $method = 'get'.ucfirst(is_string($parameterField) ? $parameterField : $parameterName);
                            $parameters[$parameterName] = $entity->$method();
                        }

                        if (isset($parameter['children'])) {
                            foreach ($parameter['children'] as $child) {
                                $method             = 'get'.ucfirst($child);
                                $parameters[$child] = $entity->$method()->getSlug();
                            }
                        }
                    } elseif (isset($parameter['name']) && isset($iParameters[$parameter['name']])) {
                        $parameters[$parameter['name']] = $iParameters[$parameter['name']];
                    }
                }
            }

            try {
                $url = $this->router->generate($route, $parameters, $absolute);
            } catch (\Exception $e) {
                $url = '#';
            }
        } elseif (isset($link['externalLink']) and $link['externalLink']) {
            $url = $link['externalLink'];
            
            if (($httpPos = strpos($link['externalLink'], 'http')) === false or $httpPos != 0) {
                $url = sprintf('http://%s', $link['externalLink']);
            }
        }

        return $url;
    }

    /**
     * @param Item $item
     *
     * @return array
     */
    public function getParameters(Item $item)
    {
        $parameters = [];

        foreach ($item->getParameters() as $itemParameter) {
            $routeParameter = $itemParameter->getParameter();
            $parameterName  = $routeParameter->getName();

            if ($routeParameter->getType()) {
                $repository = $this->entityManager->getRepository($routeParameter->getType());
                $entity     = $repository->find($itemParameter->getValue());
                // USE PROPERTY ACCESSOR !!!
                $getter         = sprintf('get%s', ucfirst($routeParameter->getValueField()));
                $parameterValue = $entity->$getter();
            } else {
                $parameterValue = $itemParameter->getValue();
            }

            $parameters[$parameterName] = $parameterValue;
        }

        return $parameters;
    }
}
