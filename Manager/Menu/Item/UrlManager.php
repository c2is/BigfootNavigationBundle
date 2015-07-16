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

    /** @var Context */
    private $context;

    /**
     * @param EntityManager $entityManager
     * @param RouterInterface $router
     * @param Context $context
     */
    public function __construct(EntityManager $entityManager, RouterInterface $router, Context $context)
    {
        $this->entityManager = $entityManager;
        $this->router        = $router;
        $this->context       = $context;
    }

    /**
     * @param Item $item
     * @param bool $absolute
     * @return string
     */
    public function getUrl(Item $item, $absolute = true)
    {
        $link = $item->getLink();

        return $this->getLink($link, $absolute);
    }

    /**
     * @param array $link
     * @param bool $absolute
     * @return string
     */
    public function getLink($link, $absolute = true)
    {
        $url = '#';

        if (isset($link['name'])) {

            $parameters  = array();
            $locale = $this->context->get('language');
            $route       = $link['name'];
            if ($this->router instanceof \BeSimple\I18nRoutingBundle\Routing\Router and $this->router->getRouteCollection()->get(sprintf('%s.%s', $route, $locale))) {
                $parameters['locale'] = $locale;
                $options     = $this->router->getRouteCollection()->get($route.'.'.$locale)->getOptions();
            } else {
                $options     = $this->router->getRouteCollection()->get($route)->getOptions();
            }
            $iParameters = $link['parameters'];

            if (isset($options['parameters'])) {
                foreach ($options['parameters'] as $parameter) {
                    if (preg_match('/Bundle/i', $parameter['type'])) {
                        $entity = $this->entityManager->getRepository($parameter['type'])->find($iParameters[$parameter['name']]);

                        if (!$entity) {
                            return '#';
                        }

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
     * @return array
     */
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
