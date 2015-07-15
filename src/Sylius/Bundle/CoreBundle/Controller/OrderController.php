<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\OrderTransitions;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrderController extends ResourceController
{
    /**
     * @param Request $request
     * @param integer $id
     *
     * @return Response
     *
     * @throws NotFoundHttpException
     */
    public function indexByCustomerAction(Request $request, $id)
    {
        $customer = $this->get('sylius.repository.customer')->findForDetailsPage($id);

        if (!$customer) {
            throw new NotFoundHttpException('Requested customer does not exist.');
        }

        $paginator = $this
            ->getRepository()
            ->createByCustomerPaginator($customer, $this->config->getSorting())
        ;

        $paginator->setCurrentPage($request->get('page', 1), true, true);
        $paginator->setMaxPerPage($this->config->getPaginationMaxPerPage());

        // Fetch and cache deleted orders
        $entityManager = $this->get('doctrine.orm.entity_manager');
        $entityManager->getFilters()->disable('softdeleteable');
        $paginator->getCurrentPageResults();
        $paginator->getNbResults();
        $entityManager->getFilters()->enable('softdeleteable');

        return $this->render('SyliusWebBundle:Backend/Order:indexByCustomer.html.twig', array(
            'customer' => $customer,
            'orders'   => $paginator
        ));
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws NotFoundHttpException
     */
    public function releaseInventoryAction(Request $request)
    {
        $order = $this->findOr404($request);

        $this->get('sm.factory')
            ->get($order, OrderTransitions::GRAPH)
            ->apply(OrderTransitions::SYLIUS_RELEASE)
        ;

        $this->domainManager->update($order);

        return $this->redirectHandler->redirectToReferer();
    }

    /**
     * Get order history changes.
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws NotFoundHttpException
     */
    public function historyAction(Request $request)
    {
        /** @var $order OrderInterface */
        $order = $this->findOr404($request);

        $repository = $this->get('doctrine')->getManager()->getRepository('Gedmo\Loggable\Entity\LogEntry');

        $items = array();
        foreach ($order->getItems() as $item) {
            $items[] = $repository->getLogEntries($item);
        }

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('history.html'))
            ->setData(array(
                'order' => $order,
                'logs'  => array(
                    'order'            => $repository->getLogEntries($order),
                    'order_items'      => $items,
                    'billing_address'  => $repository->getLogEntries($order->getBillingAddress()),
                    'shipping_address' => $repository->getLogEntries($order->getShippingAddress()),
                ),
            ))
        ;

        return $this->handleView($view);
    }
}
