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

use FOS\RestBundle\View\View;
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
        $configuration = $this->configurationFactory->create($this->metadata, $request);
        $customer = $this->container->get('sylius.repository.customer')->findForDetailsPage($id);

        if (!$customer) {
            throw new NotFoundHttpException('Requested customer does not exist.');
        }

        $paginator = $this->repository->createByCustomerPaginator($customer, $configuration->getSorting());

        $paginator->setCurrentPage($request->get('page', 1), true, true);
        $paginator->setMaxPerPage($configuration->getPaginationMaxPerPage());

        $entityManager = $this->container->get('doctrine.orm.entity_manager');
        $entityManager->getFilters()->disable('softdeleteable');
        $paginator->getCurrentPageResults();
        $paginator->getNbResults();

        $entityManager->getFilters()->enable('softdeleteable');

        $view = View::create()
            ->setTemplate($configuration->getTemplate('index.html'))
            ->setData(array(
                'customer' => $customer,
                'orders'   => $paginator,
            ))
        ;

        return $this->handleView($configuration, $view);
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
        $configuration = $this->configurationFactory->create($this->metadata, $request);

        /** @var $order OrderInterface */
        $order = $this->findOr404($configuration);

        $repository = $this->container->get('doctrine')->getManager()->getRepository('Gedmo\Loggable\Entity\LogEntry');

        $items = array();
        foreach ($order->getItems() as $item) {
            $items[] = $repository->getLogEntries($item);
        }

        $view = View::create()
            ->setTemplate($configuration->getTemplate('history.html'))
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

        return $this->handleView($configuration, $view);
    }
}
