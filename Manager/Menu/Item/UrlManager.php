<?php

namespace Bigfoot\Bundle\NavigationBundle\Manager\Menu\Item;

use Bigfoot\Bundle\NavigationBundle\Entity\Menu\Item;
use Bigfoot\Bundle\NavigationBundle\Entity\Menu\Item\Parameter;
use Doctrine\ORM\EntityManager;
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

    /**
     * @param EntityManager $entityManager
     * @param RouterInterface $router
     */
    public function __construct(EntityManager $entityManager, RouterInterface $router)
    {
        $this->entityManager    = $entityManager;
        $this->router           = $router;
    }

    public function getUrl(Item $item)
    {
        $url = '#';

        if ($externalLink = $item->getExternalLink()) {
            $url = ($httpPos = strpos($externalLink, 'http')) === false or $httpPos != 0 ? sprintf('http://%s', $externalLink) : $externalLink;
        } elseif ($route = $item->getRoute()) {
            $parameters = array();

            /**
             * @var Parameter $itemParameter
             */
            foreach ($item->getParameters() as $itemParameter) {
                $routeParameter = $itemParameter->getParameter();
                $parameterName = $routeParameter->getName();
                if ($routeParameter->getType()) {
                    $repository = $this->entityManager->getRepository($routeParameter->getType());
                    $entity = $repository->find($itemParameter->getValue());
                    $getter = sprintf('get%s', ucfirst($routeParameter->getValueField()));
                    $parameterValue = $entity->$getter();
                } else {
                    $parameterValue = $itemParameter->getValue();
                }

                $parameters[$parameterName] = $parameterValue;
            }

            $url = $this->router->generate($item->getRoute()->getName(), $parameters);
        }

        return $url;
    }
}
