<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SalesBundle\Controller\Backend;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Status backend controller.
 * Provides basic CRUD for order statuses management.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class StatusController extends ContainerAware
{
    /**
     * Lists statuses.
     *
     * @return Response
     */
    public function listAction()
    {
        $statuses = $this->container->get('sylius_sales.manager.status')->findStatuses();

        return $this->container->get('templating')->renderResponse('SyliusSalesBundle:Backend/Status:list.html.'.$this->getEngine(), array(
            'statuses'  => $statuses
        ));
    }

    /**
     * Creates a new status.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        $status = $this->container->get('sylius_sales.manager.status')->createStatus();

        $form = $this->container->get('form.factory')->create('sylius_sales_status');
        $form->setData($status);

        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $this->container->get('sylius_sales.manager.status')->persistStatus($status);

                return new RedirectResponse($this->container->get('router')->generate('sylius_sales_backend_status_list'));
            }
        }

        return $this->container->get('templating')->renderResponse('SyliusSalesBundle:Backend/Status:create.html.'.$this->getEngine(), array(
            'form' => $form->createView()
        ));
    }

    /**
     * Updates a status.
     *
     * @param Request $request
     * @param mixed   $id
     *
     * @return Response
     */
    public function updateAction(Request $request, $id)
    {
        $status = $this->findStatusOr404($id);

        $form = $this->container->get('form.factory')->create('sylius_sales_status');
        $form->setData($status);

        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $this->container->get('sylius_sales.manager.status')->persistStatus($status);

                return new RedirectResponse($this->container->get('router')->generate('sylius_sales_backend_status_list'));
            }
        }

        return $this->container->get('templating')->renderResponse('SyliusSalesBundle:Backend/Status:update.html.' . $this->getEngine(), array(
            'form'   => $form->createView(),
            'status' => $status
        ));
    }

    /**
     * Deletes statuses.
     *
     * @param mixed $id
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $status = $this->findStatusOr404($id);

        $this->container->get('sylius_sales.manager.status')->removeStatus($status);

        return new RedirectResponse($this->container->get('request')->headers->get('referer'));
    }

    /**
     * Moves status up.
     *
     * @param mixed $id
     *
     * @return Response
     */
    public function moveUpAction($id)
    {
        $status = $this->findStatusOr404($id);

        $this->container->get('sylius_sales.manager.status')->moveStatusUp($status);

        return new RedirectResponse($this->container->get('request')->headers->get('referer'));
    }

    /**
     * Moves status down.
     *
     * @param mixed $id
     *
     * @return Response
     */
    public function moveDownAction($id)
    {
        $status = $this->findStatusOr404($id);

        $this->container->get('sylius_sales.manager.status')->moveStatusDown($status);

        return new RedirectResponse($this->container->get('request')->headers->get('referer'));
    }

    /**
     * Looks for a status and throws not found exception when
     * unsuccessful.
     *
     * @param mixed $id
     *
     * @throw NotFoundHttpException
     *
     * @return StatusInterface
     */
    protected function findStatusOr404($id)
    {
        if (!$status = $this->container->get('sylius_sales.manager.status')->findStatus($id)) {
            throw new NotFoundHttpException(sprintf('Order status with id "%s" does not exist', $id));
        }

        return $status;
    }

    /**
     * Returns templating engine name.
     *
     * @return string
     */
    protected function getEngine()
    {
        return $this->container->getParameter('sylius_sales.engine');
    }

}
