<?php

namespace Bigfoot\Bundle\NavigationBundle\Controller\Menu\Item;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

use Bigfoot\Bundle\CoreBundle\Controller\CrudController;

/**
 * Attribute controller.
 *
 * @Cache(maxage="0", smaxage="0", public="false")
 * @Route("/menu/item/attribute")
 */
class AttributeController extends CrudController
{
    /**
     * @return string
     */
    protected function getName()
    {
        return 'bigfoot_menu_item_attribute';
    }

    /**
     * @return string
     */
    protected function getEntity()
    {
        return 'BigfootNavigationBundle:Menu\Item\Attribute';
    }

    protected function getFields()
    {
        return array(
            'id'       => array(
                'label' => 'ID',
            ),
            'name'     => array(
                'label' => 'Name',
            ),
        );
    }

    protected function getFormType()
    {
        return 'bigfoot_menu_item_attribute';
    }

    /**
     * Lists Attribute entities.
     *
     * @Route("/", name="bigfoot_menu_item_attribute")
     */
    public function indexAction()
    {
        return $this->doIndex();
    }

    /**
     * New Attribute entity.
     *
     * @Route("/new", name="bigfoot_menu_item_attribute_new")
     */
    public function newAction(Request $request)
    {
        return $this->doNew($request);
    }

    /**
     * Edit Attribute entity.
     *
     * @Route("/edit/{id}", name="bigfoot_menu_item_attribute_edit")
     */
    public function editAction(Request $request, $id)
    {
        return $this->doEdit($request, $id);
    }

    /**
     * Delete Attribute entity.
     *
     * @Route("/delete/{id}", name="bigfoot_menu_item_attribute_delete")
     */
    public function deleteAction(Request $request, $id)
    {
        return $this->doDelete($request, $id);
    }
}
