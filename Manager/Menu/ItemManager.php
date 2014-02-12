<?php

namespace Bigfoot\Bundle\NavigationBundle\Manager\Menu;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Doctrine\ORM\EntityManager;

use Bigfoot\Bundle\NavigationBundle\Entity\Menu\Item\Parameter as ItemParameter;

/**
 * Item Manager
 */
class ItemManager
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function addItem($item, $request)
    {
        $parameters = $this->getItemParameters($request);

        $this->handleItemParameters($item, $parameters);
    }

    public function editItem($item, $request)
    {
        $parameters   = $this->getItemParameters($request);
        $dbParameters = $item->getParameters();

        foreach ($dbParameters as $dbParameter) {
            $this->entityManager->remove($dbParameter);
        }

        $this->handleItemParameters($item, $parameters);
    }

    public function getItemParameters($request)
    {
        $forms = $request->request->all();

        if (isset($forms['admin_menu_item']['parameters'])) {
            return $parameters = $forms['admin_menu_item']['parameters'];
        } elseif (isset($forms['admin_route_parameter'])) {
            return $parameters = $forms['admin_route_parameter'];
        } else {
            return array();
        }
    }

    public function handleItemParameters($item, $parameters)
    {
        foreach ($parameters as $parameter) {
            if (is_array($parameter)) {
                $dbParameter = $this->entityManager->getRepository('BigfootNavigationBundle:Route\Parameter')->find($parameter['id']);
                $value       = (isset($parameter['value'])) ? $parameter['value'] : null;

                $itemParameter = new ItemParameter();
                $itemParameter->setItem($item);
                $itemParameter->setParameter($dbParameter);
                $itemParameter->setValue($value);

                $this->entityManager->persist($itemParameter);
            }
        }

        $this->entityManager->persist($item);
        $this->entityManager->flush();
    }
}
