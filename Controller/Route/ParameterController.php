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
     * @Route("/list/{route}/{field}", name="admin_route_parameter_list", options={"expose"=true})
     * @Template()
     */
    public function listAction(Request $request, $route, $field)
    {
        $item       = new Item();
        $entityForm = $this->createForm('admin_menu_item', $item);

        $entityForm
            ->get($field)
            ->add(
                'parameters',
                'admin_route_parameter',
                array(
                    'link' => $route,
                )
            );

        return array(
            'form' => $entityForm->get($field)->get('parameters')->createView()
        );
    }
}
