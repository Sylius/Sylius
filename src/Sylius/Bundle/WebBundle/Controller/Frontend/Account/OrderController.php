<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\WebBundle\Controller\Frontend\Account;

use FOS\RestBundle\Controller\FOSRestController;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Repository\OrderRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Account order controller.
 *
 * @author Julien Janvier <j.janvier@gmail.com>
 */
class OrderController extends FOSRestController
{
    /**
     * List orders of the current customer.
     *
     * @return Response
     */
    public function indexAction()
    {
        $orders = $this->getOrderRepository()->findBy(array('customer' => $this->getCustomer()), array('updatedAt' => 'desc'));

        $view = $this
            ->view()
            ->setTemplate('SyliusWebBundle:Frontend/Account:Order/index.html.twig')
            ->setData(array(
                'orders' => $orders,
            ))
        ;

        return $this->handleView($view);
    }

    /**
     * Get single order of the current customer.
     *
     * @param string $number
     *
     * @return Response
     *
     * @throws NotFoundHttpException
     * @throws AccessDeniedException
     */
    public function showAction($number)
    {
        $order = $this->findOrderOr404($number);

        $view = $this
            ->view()
            ->setTemplate('SyliusWebBundle:Frontend/Account:Order/show.html.twig')
            ->setData(array(
                'order' => $order,
            ))
        ;

        return $this->handleView($view);
    }

    /**
     * Renders an invoice as PDF.
     *
     * @param Request $request
     * @param string  $number
     *
     * @return Response
     * @throws NotFoundHttpException
     * @throws AccessDeniedException
     */
    public function renderInvoiceAction(Request $request, $number)
    {
        $order = $this->findOrderOr404($number);

        if (!$order->isInvoiceAvailable()) {
            throw $this->createNotFoundException('The invoice can not yet be generated.');
        }

        $html = $this->renderView('SyliusWebBundle:Frontend/Account:Order/invoice.html.twig', array(
            'order' => $order
        ));

        $generator = $this
            ->get('knp_snappy.pdf')
            ->getInternalGenerator();

        $generator->setOptions(array(
            'footer-left' => '[title]',
            'footer-right' => '[page]/[topage]',
            'footer-line' => true,
            'footer-font-name' => '"Helvetica Neue",​Helvetica,​Arial,​sans-serif',
            'footer-font-size' => 10,
        ));

        return new Response(
            $generator->getOutputFromHtml($html),
            200,
            array(
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $order->getNumber() . '.pdf"'
            )
        );
    }

    /**
     * @return OrderRepositoryInterface
     */
    protected function getOrderRepository()
    {
        return $this->get('sylius.repository.order');
    }

    /**
     * Finds order or throws 404
     *
     * @param string $number
     *
     * @return OrderInterface
     *
     * @throws NotFoundHttpException
     * @throws AccessDeniedException
     */
    protected function findOrderOr404($number)
    {
        /* @var $order OrderInterface */
        if (null === $order = $this->getOrderRepository()->findOneBy(array('number' => $number))) {
            throw $this->createNotFoundException('The order does not exist.');
        }

        if (!$this->get('security.context')->isGranted('ROLE_SYLIUS_ADMIN')
            && (!$this->getCustomer() || $this->getCustomer()->getId() !== $order->getCustomer()->getId())
        ) {
            throw new AccessDeniedException();
        }

        return $order;
    }

    protected function getCustomer()
    {
        return $this->get('sylius.context.customer')->getCustomer();
    }
}
