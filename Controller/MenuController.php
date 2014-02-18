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
        return 'admin_menu';
    }

    public function getFormTemplate()
    {
        return $this->getEntity().':edit.html.twig';
    }

    /**
     * Lists Menu entities.
     *
     * @Route("/", name="admin_menu")
     */
    public function indexAction()
    {
        return $this->doIndex();
    }

    /**
     * New Menu entity.
     *
     * @Route("/new", name="admin_menu_new")
     */
    public function newAction(Request $request)
    {
        return $this->doNew($request);
    }

    /**
     * Edit Menu entity.
     *
     * @Route("/edit/{id}", name="admin_menu_edit")
     */
    public function editAction(Request $request, $id)
    {
        return $this->doEdit($request, $id);
    }

    /**
     * Delete Menu entity.
     *
     * @Route("/delete/{id}", name="admin_menu_delete")
     */
    public function deleteAction(Request $request, $id)
    {
        return $this->doDelete($request, $id);
    }

    /**
     * Render form
     */
    protected function renderForm($form, $action, $menu)
    {
        $treeViews = $this->renderView(
            $this->getThemeBundle().':navigation:nestable.html.twig',
            array(
                'items'  => $menu->getLvl1Items(),
                'output' => 'admin_menu_items'
            )
        );

        return $this->render(
            $this->getFormTemplate(),
            array(
                'form'              => $form->createView(),
                'form_method'       => 'POST',
                'form_title'        => sprintf('%s creation', $this->getEntityLabel()),
                'form_action'       => $action,
                'form_submit'       => 'Submit',
                'form_cancel_route' => $this->getRouteNameForAction('index'),
                'menu'              => $menu,
                'treeViews'         => $treeViews,
            )
        );
    }
}
