<?php

namespace Bigfoot\Bundle\NavigationBundle\Services;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;


/**
 * Service DisplayMenu
 *
 * @Route("/")
 *
 */
class DisplayMenu
{

    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function displayMenuAction($menu_slug, $current_uri, $template)
    {
        $em = $this->container->get('doctrine')->getManager();
        $queryBuilder = $em->getRepository('BigfootNavigationBundle:Item')
            ->createQueryBuilder('c');
        $items = $queryBuilder
            ->leftJoin('c.menu', 'm')
            ->where('m.slug = :menu_slug and c.parent is null')
            ->orderBy('c.position')
            ->setParameter('menu_slug', $menu_slug)
            ->getQuery()->getResult()
        ;

        $arrayPath = array();

        foreach ($items as $item) {

            $tempParameters = $item->getParameters();
            $tabParameters  = array();

            if ($tempParameters) {
                foreach ($tempParameters as $parameter) {
                    $tabParameters[$parameter->getParameter()] = $parameter->getValue();
                }
            }

            $externalLink = $item->getExternalLink();
            $uri = '';

            if ($externalLink != '') {
                $externalLink = (strstr($externalLink,'www') && !strstr($externalLink,'http://')) ? 'http://'.$externalLink : $externalLink;
            }
            else {
                $uri = $this->container->get('router')->generate($item->getRoute(),$tabParameters);
            }


            $arrayPath[] = array(
                'label'         =>  $item->getName(),
                'route'         =>  $item->getRoute(),
                'parameters'    =>  $tabParameters,
                'attribute'     =>  $item->getAttribute(),
                'external_link' =>  $externalLink,
                'uri'           =>  $uri,
            );
        }

        $arrayMenu = array();

        foreach ($arrayPath as $path) {

            $current = ($current_uri == $path['route']) ? true : false;

            $arrayMenu[] = array(
                'route'         => $path['route'],
                'uri'           => $path['uri'],
                'current'       => $current,
                'label'         => $path['label'],
                'parameters'    => $path['parameters'],
                'attribute'     => $path['attribute'],
                'external_link'     => $path['external_link'],
            );
        }

        return $this->container->get('templating')->render($template, array(
            'arrayMenu' => $arrayMenu
        ));
    }
}
