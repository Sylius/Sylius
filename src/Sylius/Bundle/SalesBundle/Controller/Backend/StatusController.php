<?php

namespace Sylius\Bundle\SalesBundle\Controller\Backend;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\ContainerAware;

class StatusController extends ContainerAware
{
    /**
     * Shows a status.
     */
    public function showAction($id)
    {
        $status = $this->container->get('sylius_sales.manager.status')->findStatus($id);

        if (!$status) {
            throw new NotFoundHttpException('Requested status does not exist.');
        }

        return $this->container->get('templating')->renderResponse('SyliusSalesBundle:Backend/Status:show.html.' . $this->getEngine(), array(
            'status' => $status
        ));
    }

    /**
     * Lists statuses.
     */
    public function listAction()
    {
        $statusManager = $this->container->get('sylius_sales.manager.status');
        $statusSorter = $this->container->get('sylius_sales.sorter.status');

        $paginator = $statusManager->createPaginator($statusSorter);
        $paginator->setCurrentPage($this->container->get('request')->query->get('page', 1), true, true);

        $statuses = $paginator->getCurrentPageResults();

        return $this->container->get('templating')->renderResponse('SyliusSalesBundle:Backend/Status:list.html.' . $this->getEngine(), array(
            'statuses'  => $statuses,
            'paginator' => $paginator,
            'sorter'    => $statusSorter
        ));
    }

    /**
     * Creates a new status.
     */
    public function createAction()
    {
        $request = $this->container->get('request');

        $status = $this->container->get('sylius_sales.manager.status')->createStatus();

        $form = $this->container->get('form.factory')->create($this->container->get('sylius_sales.form.type.status'));
        $form->setData($status);

        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $this->container->get('sylius_sales.manager.status')->persistStatus($status);

                return new RedirectResponse($this->container->get('router')->generate('sylius_sales_backend_status_update', array(
                    'id' => $status->getId()
                )));
            }
        }

        return $this->container->get('templating')->renderResponse('SyliusSalesBundle:Backend/Status:create.html.' . $this->getEngine(), array(
            'form' => $form->createView()
        ));
    }

    /**
     * Updates a status.
     */
    public function updateAction($id)
    {
        $status = $this->container->get('sylius_sales.manager.status')->findStatus($id);

        if (!$status) {
            throw new NotFoundHttpException('Requested status does not exist.');
        }

        $request = $this->container->get('request');

        $form = $this->container->get('form.factory')->create($this->container->get('sylius_sales.form.type.status'));
        $form->setData($status);

        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $this->container->get('sylius_sales.manager.status')->persistStatus($status);

                return new RedirectResponse($this->container->get('router')->generate('sylius_sales_backend_status_show', array(
                    'id' => $status->getId()
                )));
            }
        }

        return $this->container->get('templating')->renderResponse('SyliusSalesBundle:Backend/Status:update.html.' . $this->getEngine(), array(
            'form'    => $form->createView(),
            'status' => $status
        ));
    }

    /**
     * Deletes statuses.
     */
    public function deleteAction($id)
    {
        $status = $this->container->get('sylius_sales.manager.status')->findStatus($id);

        if (!$status) {
            throw new NotFoundHttpException('Requested status does not exist.');
        }

        $this->container->get('sylius_sales.manager.status')->removeStatus($status);

        return new RedirectResponse($this->container->get('request')->headers->get('referer'));
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