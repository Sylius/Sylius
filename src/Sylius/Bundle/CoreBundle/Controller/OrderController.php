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
use Gedmo\Loggable\Entity\LogEntry;
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
     * @param int $id
     *
     * @return Response
     *
     * @throws NotFoundHttpException
     */
    public function indexByCustomerAction(Request $request, $id)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
        $customer = $this->container->get('sylius.repository.customer')->findForDetailsPage($id);

        if (!$customer) {
            throw new NotFoundHttpException('Requested customer does not exist.');
        }

        $paginator = $this->repository->createPaginatorByCustomer($customer, $configuration->getSorting());

        $paginator->setCurrentPage($request->get('page', 1), true, true);
        $paginator->setMaxPerPage($configuration->getPaginationMaxPerPage());

        // Fetch and cache deleted orders
        $paginator->getCurrentPageResults();
        $paginator->getNbResults();

        return $this->container->get('templating')->renderResponse('SyliusWebBundle:Backend/Order:indexByCustomer.html.twig', [
            'customer' => $customer,
            'orders' => $paginator,
        ]);
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
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
        $order = $this->findOr404($configuration);

        $this->container->get('sm.factory')
            ->get($order, OrderTransitions::GRAPH)
            ->apply(OrderTransitions::SYLIUS_RELEASE)
        ;

        $this->manager->flush();

        return $this->redirectHandler->redirectToReferer($configuration);
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws NotFoundHttpException
     */
    public function historyAction(Request $request)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
        /** @var $order OrderInterface */
        $order = $this->findOr404($configuration);

        $repository = $this->get('doctrine')->getManager()->getRepository(LogEntry::class);

        $items = [];
        foreach ($order->getItems() as $item) {
            $items[] = $repository->getLogEntries($item);
        }

        $view = View::create()
            ->setTemplate($configuration->getTemplate('history.html'))
            ->setData([
                'order' => $order,
                'logs' => [
                    'order' => $repository->getLogEntries($order),
                    'order_items' => $items,
                    'billing_address' => $repository->getLogEntries($order->getBillingAddress()),
                    'shipping_address' => $repository->getLogEntries($order->getShippingAddress()),
                ],
            ])
        ;

        return $this->viewHandler->handle($configuration, $view);
    }
}
