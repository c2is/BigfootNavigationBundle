<?php

namespace Bigfoot\Bundle\NavigationBundle\Controller;

use Bigfoot\Bundle\NavigationBundle\Entity\Item;
use Bigfoot\Bundle\NavigationBundle\Entity\ItemParameter;
use Bigfoot\Bundle\NavigationBundle\Form\ItemParameterType;
use Bigfoot\Bundle\NavigationBundle\Form\ItemType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Bigfoot\Bundle\CoreBundle\Crud\CrudController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Item controller.
 *
 * @Route("/menu/item")
 */
class ItemController extends CrudController
{
    /**
     * @return string
     */
    protected function getName()
    {
        return 'admin_menu_item';
    }

    /**
     * @return string
     */
    protected function getEntity()
    {
        return 'BigfootNavigationBundle:Item';
    }

    protected function getFields()
    {
        return array(
            'id' => 'ID',
            'name' => 'Name',
        );
    }

    protected function getFormType()
    {
        return 'bigfoot_menu_item';
    }

    /**
     * Lists all Item entities.
     *
     * @Route("/", name="admin_menu_item")
     * @Method("GET")
     * @Template("BigfootCoreBundle:crud:index.html.twig")
     */
    public function indexAction()
    {
        return $this->doIndex();
    }
    /**
     * Creates a new Item entity.
     *
     * @Route("/", name="admin_menu_item_create")
     * @Method("POST")
     * @Template("BigfootCoreBundle:crud:new.html.twig")
     */
    public function createAction(Request $request)
    {
        return $this->doCreate($request);
    }

    /**
     * Displays a form to create a new Item entity.
     *
     * @Route("/new", name="admin_menu_item_new")
     * @Method("GET")
     * @Template("BigfootNavigationBundle:item:edit.html.twig")
     */
    public function newAction()
    {

        return $this->doNew();
    }

    /**
     * Displays a form to edit an existing Item entity.
     *
     * @Route("/{id}/edit", name="admin_menu_item_edit")
     * @Method("GET")
     * @Template("BigfootNavigationBundle:item:edit.html.twig")
     */
    public function editAction($id)
    {

        return $this->doEdit($id);
    }

    /**
     * Edits an existing Item entity.
     *
     * @Route("/{id}", name="admin_menu_item_update")
     * @Method("PUT")
     * @Template("BigfootCoreBundle:crud:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {

        return $this->doUpdate($request, $id);
    }

    /**
     * Deletes a Item entity.
     *
     * @Route("/{id}", name="admin_menu_item_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        return $this->doDelete($request, $id);
    }

    /**
     * Lists all parameters for a given route.
     *
     * @Route("/parameters/{route}", name="admin_menu_item_route_parameters", defaults={"route": null})
     * @Method("GET")
     * @Template("BigfootNavigationBundle:includes:parameters.html.twig")
     */
    public function listParametersAction(Request $request, $route)
    {
        $parameters = array();

        $routes = $this->get('bigfoot.route_manager')->getRoutes();
        if (isset($routes[$route]) and array_key_exists('parameters', $routeOptions = $routes[$route]->getOptions())) {
            $parameters = $routeOptions['parameters'];
        }

        $item = new Item();
        foreach ($parameters as $parameter => $type) {
            $objParameter = new ItemParameter();
            $objParameter->setParameter($parameter);
            $objParameter->setType($type);

            $item->addParameter($objParameter);
        }

        $form = $this->createForm(new ItemType($this->get('bigfoot.route_manager')), $item);

        return array(
            'form' => $form->createView(),
        );
    }
}
