<?php

namespace Bigfoot\Bundle\NavigationBundle\Controller;

use Bigfoot\Bundle\NavigationBundle\Entity\Item;
use Bigfoot\Bundle\NavigationBundle\Entity\ItemParameter;
use Bigfoot\Bundle\NavigationBundle\Form\ItemParameterType;
use Bigfoot\Bundle\NavigationBundle\Form\ItemType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Bigfoot\Bundle\CoreBundle\Crud\CrudController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Item controller.
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
        $em = $this->container->get('doctrine')->getManager();

        $entity = $em->getRepository($this->getEntity())->find($id);

        if (!$entity) {
            throw new NotFoundHttpException(sprintf('Unable to find %s entity.', $this->getEntity()));
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->container->get('form.factory')->create($this->getFormType(), $entity);
        $editForm->submit($request);

        foreach ($entity->getParameters() as $parameter) {
            if ($parameter->getType()) {var_dump($parameter->getValueField());
                $getValueMethod = sprintf('get%s', ucfirst($parameter->getValueField()));
                $parameter->setValue($parameter->getValue()->$getValueMethod());
            }
        }

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            $this->container->get('session')->getFlashBag()->add(
                'success',
                $this->container->get('templating')->render('BigfootCoreBundle:includes:flash.html.twig', array(
                    'icon' => 'ok',
                    'heading' => 'Success!',
                    'message' => sprintf('The %s has been updated.', $this->getEntityName()),
                    'actions' => array(
                        array(
                            'route' => $this->container->get('router')->generate($this->getRouteNameForAction('index')),
                            'label' => 'Back to the listing',
                            'type'  => 'success',
                        ),
                    )
                ))
            );

            return new RedirectResponse($this->container->get('router')->generate($this->getRouteNameForAction('edit'), array('id' => $id)));
        }

        return array(
            'form'                  => $editForm->createView(),
            'form_method'           => 'PUT',
            'form_action'           => $this->container->get('router')->generate($this->getRouteNameForAction('update'), array('id' => $entity->getId())),
            'form_cancel_route'     => $this->getRouteNameForAction('index'),
            'form_title'            => sprintf('%s edit', $this->getEntityLabel()),
            'delete_form'           => $deleteForm->createView(),
            'delete_form_action'    => $this->container->get('router')->generate($this->getRouteNameForAction('delete'), array('id' => $entity->getId())),
            'isAjax'                => $this->get('request')->isXmlHttpRequest(),
            'breadcrumbs'       => array(
                array(
                    'url'   => $this->container->get('router')->generate($this->getRouteNameForAction('index')),
                    'label' => $this->getEntityLabelPlural()
                ),
                array(
                    'url'   => $this->container->get('router')->generate($this->getRouteNameForAction('edit'), array('id' => $entity->getId())),
                    'label' => sprintf('%s edit', $this->getEntityLabel())
                ),
            ),
        );
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

        $routes = $this->container->get('bigfoot.route_manager')->getRoutes();
        if (isset($routes[$route]) and array_key_exists('parameters', $routeOptions = $routes[$route]->getOptions())) {
            $parameters = $routeOptions['parameters'];
        }

//        var_dump($parameters);die;

        $item = new Item();
        foreach ($parameters as $parameter) {

            $objParameter = new ItemParameter();
            $objParameter->setParameter($parameter['name']);
            $objParameter->setType(isset($parameter['type']) ? $parameter['type'] : null);
            $objParameter->setLabelField(isset($parameter['label']) ? $parameter['label'] : null);
            $objParameter->setValueField(isset($parameter['value']) ? $parameter['value'] : 'id');

            $item->addParameter($objParameter);
        }

        $form = $this->container->get('form.factory')->create(new ItemType($this->container->get('bigfoot.route_manager')), $item);

        return array(
            'form' => $form->createView(),
        );
    }
}
