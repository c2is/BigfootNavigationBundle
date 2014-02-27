<?php

namespace Bigfoot\Bundle\NavigationBundle\Controller\Menu;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

use Bigfoot\Bundle\CoreBundle\Controller\CrudController;
use Bigfoot\Bundle\NavigationBundle\Entity\Menu\Item;
use Bigfoot\Bundle\NavigationBundle\Entity\Menu\Item\Parameter as ItemParameter;
use Bigfoot\Bundle\NavigationBundle\Form\Type\Menu\ItemType;
use Bigfoot\Bundle\NavigationBundle\Form\Type\Menu\Item\ParameterType;

/**
 * Item Controller
 *
 * @Cache(maxage="0", smaxage="0", public="false")
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
        return 'BigfootNavigationBundle:Menu\Item';
    }

    protected function getFields()
    {
        return array(
            'id'   => 'ID',
            'name' => 'Name',
            'menu' => 'Menu',
        );
    }

    protected function getFormType()
    {
        return 'admin_menu_item';
    }

    public function getFormTemplate()
    {
        return 'BigfootNavigationBundle:Menu\Item:edit.html.twig';
    }

    /**
     * Lists Item entities.
     *
     * @Route("/", name="admin_menu_item")
     */
    public function indexAction()
    {
        return $this->doIndex();
    }

    /**
     * New Item entity.
     *
     * @Route("/new", name="admin_menu_item_new")
     */
    public function newAction(Request $request)
    {
        return $this->doNew($request);
    }

    /**
     * Edit Item entity.
     *
     * @Route("/edit/{id}", name="admin_menu_item_edit")
     */
    public function editAction(Request $request, $id)
    {
        return $this->doEdit($request, $id);
    }

    /**
     * Edit Item tree position.
     *
     * @Route("/edit-item-tree-position/{id}/{parent}/{position}", name="admin_menu_item_edit_tree_position", options={"expose"=true})
     */
    public function editItemTreePositionAction(Request $request, $id, $parent, $position)
    {
        $item = $this->getRepository($this->getEntity())->find($id);

        if (!$item) {
            return new JsonResponse(sprintf('Unable to find %s entity.', $this->getEntity()));
        }

        if ($parent != 'false') {
            $parent = $this->getRepository($this->getEntity())->find($parent);

            if ($parent) {
                $item->setParent($parent);
            }
        } else {
            $item->setParent(null);
        }

        $this->persistAndFlush($item);
        $item->setPosition($position);
        $this->persistAndFlush($item);

        return new JsonResponse(true);
    }

    /**
     * Delete Item entity.
     *
     * @Route("/delete/{id}", name="admin_menu_item_delete", options={"expose"=true})
     */
    public function deleteAction(Request $request, $id)
    {
        return $this->doDelete($request, $id);
    }

    /**
     * PrePersist Item entity.
     */
    protected function prePersist($item, $action)
    {
        if ($action == 'new') {
            $this->getMenuItemManager()->addItem($item, $this->getRequest());
        } elseif ($action == 'edit') {
            $this->getMenuItemManager()->editItem($item, $this->getRequest());
        }
    }

    /**
     * Handle success response.
     */
    protected function handleSuccessResponse($action, $item = null)
    {
        if ($action == 'delete') {
            return $this->renderAjax(true, 'Success, please wait...');
        }

        $itemView = $this->renderView(
            $this->getThemeBundle().':navigation:item.html.twig',
            array('item' => $item)
        );

        $content = array(
            'itemId'   => $item->getId(),
            'itemName' => $item->getName(),
            'parent'   => ($item->getParent()) ? $item->getParent()->getId() : null,
            'view'     => $itemView
        );

        return $this->renderAjax(true, 'Success, please wait...', $content);
    }
}
