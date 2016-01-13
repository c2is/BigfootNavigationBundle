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
        return 'bigfoot_menu';
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
            'id'   => array(
                'label' => 'ID',
            ),
            'name' => array(
                'label' => 'Name',
            ),
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
     * Lists Menu entities.
     *
     * @Route("/", name="bigfoot_menu")
     * @param Request $request
     * @return array
     */
    public function indexAction(Request $request)
    {
        return $this->doIndex($request);
    }

    /**
     * New Menu entity.
     *
     * @Route("/new", name="bigfoot_menu_new")
     */
    public function newAction(Request $request)
    {
        return $this->doNew($request);
    }

    /**
     * Edit Menu entity.
     *
     * @Route("/edit/{id}", name="bigfoot_menu_edit")
     */
    public function editAction(Request $request, $id)
    {
        return $this->doEdit($request, $id);
    }

    /**
     * Delete Menu entity.
     *
     * @Route("/delete/{id}", name="bigfoot_menu_delete")
     */
    public function deleteAction(Request $request, $id)
    {
        return $this->doDelete($request, $id);
    }

    /**
     * Render form
     */
    protected function renderForm(Request $request, $form, $action, $menu, $visibility = null)
    {
        $treeViews = $this->renderView(
            $this->getThemeBundle().':navigation:nestable.html.twig',
            array(
                'items'  => $menu->getLvl1Items(),
                'output' => 'bigfoot_menu_items'
            )
        );

        return $this->render(
            $this->getFormTemplate(),
            array(
                'form'        => $form->createView(),
                'form_method' => 'POST',
                'form_title'  => sprintf('%s creation', $this->getEntityLabel()),
                'form_action' => $action,
                'form_submit' => 'Submit',
                'form_cancel' => $this->getRouteNameForAction('index'),
                'menu'        => $menu,
                'treeViews'   => $treeViews,
            )
        );
    }
}
