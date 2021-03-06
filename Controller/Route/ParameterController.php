<?php

namespace Bigfoot\Bundle\NavigationBundle\Controller\Route;

use Bigfoot\Bundle\NavigationBundle\Form\Type\Menu\ItemType;
use Bigfoot\Bundle\NavigationBundle\Form\Type\Route\ParameterType;
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
     * @Route("/list/{route}/{formName}/{fieldName}", name="bigfoot_route_parameter_list", options={"expose"=true})
     * @Template("BigfootNavigationBundle:Route:Parameter/list.html.twig")
     */
    public function listAction(Request $request, $route, $formName, $fieldName)
    {
        $entityForm = $this->createForm($formName);

        $entityForm
            ->get($fieldName)
            ->add(
                'parameters',
                ParameterType::class,
                array(
                    'link' => $route,
                )
            );

        return array(
            'form' => $entityForm->get($fieldName)->get('parameters')->createView()
        );
    }
}
