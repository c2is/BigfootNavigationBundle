<?php

namespace Bigfoot\Bundle\NavigationBundle\Controller\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

use Bigfoot\Bundle\CoreBundle\Controller\BaseController;
use Bigfoot\Bundle\NavigationBundle\Entity\Menu\Item;
use Bigfoot\Bundle\NavigationBundle\Entity\Menu\Item\Parameter;

/**
 * Parameter Controller
 *
 * @Route("/route/parameter")
 */
class ParameterController extends BaseController
{
    /**
     * Lists all parameters for a given route.
     *
     * @Route("/list/{route}", name="admin_route_parameter_list", defaults={"route": null}, options={"expose"=true})
     * @Template()
     */
    public function listAction(Request $request, $route)
    {
        $route = $this->getRepository('BigfootNavigationBundle:Route')->find($route);
        $form  = $this->createForm('admin_route_parameter', null, array('data' => array('route' => $route)));

        return array(
            'form' => $form->createView()
        );
    }
}
