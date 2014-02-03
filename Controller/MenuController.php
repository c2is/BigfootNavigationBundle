<?php

namespace Bigfoot\Bundle\NavigationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

use Bigfoot\Bundle\CoreBundle\Controller\CrudController;

/**
 * Menu controller.
 *
 * @Cache(maxage="0", smaxage="0", public="false")
 * @Route("/menu")
 */
class MenuController extends CrudController
{
    /**
     * @return string
     */
    protected function getName()
    {
        return 'admin_menu';
    }

    /**
     * @return string
     */
    protected function getEntity()
    {
        return 'BigfootNavigationBundle:Menu';
    }

    protected function getFields()
    {
        return array(
            'id'   => 'ID',
            'name' => 'Name',
        );
    }

    protected function getFormType()
    {
        return 'bigfoot_menu';
    }

    public function getFormTemplate()
    {
        return $this->getEntity().':edit.html.twig';
    }

    /**
     * Lists all Menu entities.
     *
     * @Route("/", name="admin_menu")
     * @Method("GET")
     */
    public function indexAction()
    {
        return $this->doIndex();
    }
    /**
     * Creates a new Menu entity.
     *
     * @Route("/", name="admin_menu_create")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        return $this->doCreate($request);
    }

    /**
     * Displays a form to create a new Menu entity.
     *
     * @Route("/new", name="admin_menu_new")
     * @Method("GET")
     */
    public function newAction()
    {
        return $this->doNew();
    }

    /**
     * Displays a form to edit an existing Menu entity.
     *
     * @Route("/{id}/edit", name="admin_menu_edit")
     * @Method("GET")
     */
    public function editAction($id)
    {
        return $this->doEdit($id);
    }

    /**
     * Edits an existing Menu entity.
     *
     * @Route("/{id}", name="admin_menu_update")
     * @Method("GET|PUT")
     */
    public function updateAction(Request $request, $id)
    {
        return $this->doUpdate($request, $id);
    }
    /**
     * Deletes a Menu entity.
     *
     * @Route("/{id}/delete", name="admin_menu_delete")
     * @Method("GET|DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        return $this->doDelete($request, $id);
    }
}
