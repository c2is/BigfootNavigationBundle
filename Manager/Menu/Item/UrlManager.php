<?php

namespace Bigfoot\Bundle\NavigationBundle\Manager\Menu\Item;

use Symfony\Component\Routing\RouterInterface;
use Doctrine\ORM\EntityManager;

use Bigfoot\Bundle\NavigationBundle\Entity\Menu\Item;
use Bigfoot\Bundle\NavigationBundle\Entity\Menu\Item\Parameter;

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

    /**
     * @param EntityManager $entityManager
     * @param RouterInterface $router
     */
    public function __construct(EntityManager $entityManager, RouterInterface $router)
    {
        $this->entityManager = $entityManager;
        $this->router        = $router;
    }

    public function getUrl(Item $item)
    {
        $url = '#';

        if ($externalLink = $item->getExternalLink()) {
            $url = ($httpPos = strpos($externalLink, 'http')) === false or $httpPos != 0 ? sprintf('http://%s', $externalLink) : $externalLink;
        } elseif ($route = $item->getRoute()) {
            $url = $this->router->generate($item->getRoute()->getName(), $this->getParameters($item));
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
