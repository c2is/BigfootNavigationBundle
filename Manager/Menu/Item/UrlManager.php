<?php

namespace Bigfoot\Bundle\NavigationBundle\Manager\Menu\Item;

use Symfony\Component\Routing\RouterInterface;
use Doctrine\ORM\EntityManager;

use Bigfoot\Bundle\NavigationBundle\Entity\Menu\Item;
use Bigfoot\Bundle\NavigationBundle\Entity\Menu\Item\Parameter;
use Bigfoot\Bundle\ContextBundle\Service\ContextService as Context;

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

    /** @var \Bigfoot\Bundle\ContextBundle\Service\ContextService */
    private $context;

    /**
     * @param EntityManager $entityManager
     * @param RouterInterface $router
     */
    public function __construct(EntityManager $entityManager, RouterInterface $router, Context $context)
    {
        $this->entityManager = $entityManager;
        $this->router        = $router;
        $this->context       = $context;
    }

    public function getUrl(Item $item, $absolute = false)
    {
        $url  = '#';
        $link = $item->getLink();

        if (isset($link['name'])) {
            $route       = $link['name'];
            $options     = $this->router->getRouteCollection()->get($route)->getOptions();
            $parameters  = array();
            $iParameters = $link['parameters'];

            if (isset($options['parameters'])) {
                foreach ($options['parameters'] as $parameter) {
                    if (preg_match('/Bundle/i', $parameter['type'])) {
                        $entity = $this->entityManager->getRepository($parameter['type'])->find($iParameters[$parameter['name']]);
                        $method = 'get'.ucfirst($parameter['field']);

                        $parameters[$parameter['name']] = $entity->$method();

                        if (isset($parameter['children'])) {
                            foreach ($parameter['children'] as $child) {
                                $method             = 'get'.ucfirst($child);
                                $parameters[$child] = $entity->$method()->getSlug();
                            }
                        }
                    } else {
                        $parameters[$parameter['name']] = $iParameters[$parameter['name']];
                    }
                }
            }

            $languageContext = $this->context->getContext('language');
            $locale          = $languageContext['value'];

            if ($this->router instanceof \BeSimple\I18nRoutingBundle\Routing\Router and $this->router->getRouteCollection()->get(sprintf('%s.%s', $route, $locale))) {
                $parameters['locale'] = $locale;
            }

            $url = $this->router->generate($route, $parameters, $absolute);
        } elseif (isset($link['externalLink']) and $link['externalLink']) {
            $url = $link['externalLink'];
            if (($httpPos = strpos($link['externalLink'], 'http')) === false or $httpPos != 0) {
                $url = sprintf('http://%s', $link['externalLink']);
            }
        }

        return $url;
    }

    public function getParameters(Item $item)
    {
        $parameters = array();

        foreach ($item->getParameters() as $itemParameter) {
            $routeParameter = $itemParameter->getParameter();
            $parameterName  = $routeParameter->getName();

            if ($routeParameter->getType()) {
                $repository     = $this->entityManager->getRepository($routeParameter->getType());
                $entity         = $repository->find($itemParameter->getValue());
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
