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

use Sylius\Bundle\SalesBundle\EventDispatcher\Event\FilterOrderEvent;
use Sylius\Bundle\SalesBundle\EventDispatcher\SyliusSalesEvents;
use Sylius\Bundle\SalesBundle\Form\Type\OrderType;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Backend orders controller.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class OrderController extends ContainerAware
{
    /**
     * Displays all orders.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function listAction(Request $request)
    {
        $orderManager = $this->container->get('sylius_sales.manager.order');

        $orderSorter = $this->container->get('sylius_sales.sorter.order');
        $orderFilter = $this->container->get('sylius_sales.filter.order');

        $paginator = $orderManager->createPaginator($orderSorter, $orderFilter);
        $paginator->setCurrentPage($request->query->get('page', 1));

        $orders = $paginator->getCurrentPageResults();

        return $this->container->get('templating')->renderResponse('SyliusSalesBundle:Backend/Order:list.html.'.$this->getEngine(), array(
            'orders'    => $orders,
            'paginator' => $paginator,
            'sorter'    => $orderSorter,
            'filter'    => $orderFilter
        ));
    }

    /**
     * Shows an order.
     *
     * @param mixed $id
     *
     * @return Response
     */
    public function showAction($id)
    {
        $order = $this->findOrderOr404($id);

        return $this->container->get('templating')->renderResponse('SyliusSalesBundle:Backend/Order:show.html.'.$this->getEngine(), array(
            'order' => $order
        ));
    }

    /**
     * Creates a new order.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        $order = $this->container->get('sylius_sales.manager.order')->createorder();

        $form = $this->container->get('form.factory')->create('sylius_sales_order', $order, array(
            'mode' => OrderType::MODE_CREATE
        ));

        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $this->container->get('event_dispatcher')->dispatch(SyliusSalesEvents::ORDER_CREATE, new FilterOrderEvent($order));
                $this->container->get('sylius_sales.manipulator.order')->create($order);

                return new RedirectResponse($this->container->get('router')->generate('sylius_sales_backend_order_show', array(
                    'id' => $order->getId()
                )));
            }
        }

        return $this->container->get('templating')->renderResponse('SyliusSalesBundle:Backend/Order:create.html.'.$this->getEngine(), array(
            'form' => $form->createView()
        ));
    }

    /**
     * Updates a order.
     *
     * @param Request $request
     * @param mixed   $id
     *
     * @return Response
     */
    public function updateAction(Request $request, $id)
    {
        $order = $this->findOrderOr404($id);

        $form = $this->container->get('form.factory')->create('sylius_sales_order', $order, array(
            'mode' => OrderType::MODE_UPDATE
        ));

        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $this->container->get('event_dispatcher')->dispatch(SyliusSalesEvents::ORDER_UPDATE, new FilterOrderEvent($order));
                $this->container->get('sylius_sales.manipulator.order')->update($order);

                return new RedirectResponse($this->container->get('router')->generate('sylius_sales_backend_order_show', array(
                    'id' => $order->getId()
                )));
            }
        }

        return $this->container->get('templating')->renderResponse('SyliusSalesBundle:Backend/Order:update.html.' . $this->getEngine(), array(
            'form'  => $form->createView(),
            'order' => $order
        ));
    }

    /**
     * Order status management.
     *
     * @param Request $request
     * @param mixed   $id
     *
     * @return Response
     */
    public function changeStatusAction(Request $request, $id)
    {
        $order = $this->findOrderOr404($id);

        $form = $this->container->get('form.factory')->create('sylius_sales_order', $order, array(
            'mode' => OrderType::MODE_CHANGE_STATUS
        ));

        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $this->container->get('event_dispatcher')->dispatch(SyliusSalesEvents::ORDER_CHANGE_STATUS, new FilterOrderEvent($order));
                $this->container->get('sylius_sales.manipulator.order')->changeStatus($order);

                return new RedirectResponse($request->headers->get('referer'));
            }
        }

        return $this->container->get('templating')->renderResponse('SyliusSalesBundle:Backend/Order:changeStatus.html.'.$this->getEngine(), array(
            'form'  => $form->createView(),
            'order' => $order
        ));
    }

    /**
     * Confirms order.
     *
     * @param mixed $id
     *
     * @return Response
     */
    public function confirmAction($id)
    {
        $order = $this->findOrderOr404($id);

        $this->container->get('event_dispatcher')->dispatch(SyliusSalesEvents::ORDER_CONFIRM, new FilterOrderEvent($order));
        $this->container->get('sylius_sales.manipulator.order')->confirm($order);

        return $this->redirectToOrderList();
    }

    /**
     * Closes order.
     *
     * @param mixed $id
     *
     * @return Response
     */
    public function closeAction($id)
    {
        $order = $this->findOrderOr404($id);

        $this->container->get('event_dispatcher')->dispatch(SyliusSalesEvents::ORDER_CLOSE, new FilterOrderEvent($order));
        $this->container->get('sylius_sales.manipulator.order')->close($order);

        return $this->redirectToOrderList();
    }

    /**
     * Opens order.
     *
     * @param mixed $id
     *
     * @return Response
     */
    public function openAction($id)
    {
        $order = $this->findOrderOr404($id);

        $this->container->get('event_dispatcher')->dispatch(SyliusSalesEvents::ORDER_OPEN, new FilterOrderEvent($order));
        $this->container->get('sylius_sales.manipulator.order')->open($order);

        return $this->redirectToOrderList();
    }

    /**
     * Deletes order.
     *
     * @param mixed $id
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $order = $this->findOrderOr404($id);

        $this->container->get('event_dispatcher')->dispatch(SyliusSalesEvents::ORDER_DELETE, new FilterOrderEvent($order));
        $this->container->get('sylius_sales.manipulator.order')->delete($order);

        return $this->redirectToOrderList();
    }

    /**
     * Looks for an order with given id, throws not found exception when
     * unsuccesful.
     *
     * @param mixed $id
     *
     * @throws NotFoundHttpException
     *
     * @return OrderInterface
     */
    protected function findOrderOr404($id)
    {
        if (!$order = $this->container->get('sylius_sales.manager.order')->findOrder($id)) {
            throw new NotFoundHttpException(sprintf('Order with id "%s" does not exist', $id));
        }

        return $order;
    }

    /**
     * Returns redirect response pointing to order list.
     *
     * @return RedirectResponse
     */
    protected function redirectToOrderList()
    {
        return new RedirectResponse($this->container->get('router')->generate('sylius_sales_backend_order_list'));
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
