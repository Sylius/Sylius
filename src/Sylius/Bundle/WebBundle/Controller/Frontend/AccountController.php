<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\WebBundle\Controller\Frontend;

use Sylius\Bundle\CoreBundle\Entity\Order;
use Sylius\Bundle\CoreBundle\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Frontend user account controller.
 *
 * @author Julien Janvier <j.janvier@gmail.com>
 */
class AccountController extends Controller
{
    /**
     * List orders of the current user
     *
     * @return Response
     */
    public function indexOrderAction()
    {
        $orders = $this
            ->getOrderRepository()
            ->getByUser($this->getUser(), array('updatedAt' => 'desc'));

        return $this->render(
            'SyliusWebBundle:Frontend/Account:Order/index.html.twig',
            array('orders' => $orders)
        );
    }

    /**
     * Get single order of the current user
     *
     * @param $number
     * @return Response
     * @throws NotFoundHttpException
     * @throws AccessDeniedException
     */
    public function showOrderAction($number)
    {
        $order = $this->findOrderOr404($number);
        $this->accessOrderOr403($order);

        return $this->render(
            'SyliusWebBundle:Frontend/Account:Order/show.html.twig',
            array('order' => $order)
        );
    }

    /**
     * Renders an invoice as PDF
     *
     * @param $number
     * @return Response
     * @throws NotFoundHttpException
     * @throws AccessDeniedException
     */
    public function generateInvoiceAction($number)
    {
        $order = $this->findOrderOr404($number);
        $this->accessOrderOr403($order);

        $html = $this->renderView('SyliusWebBundle:Frontend/Account:Order/invoice.html.twig', array(
            'order'  => $order
        ));

        $generator = $this
            ->get('knp_snappy.pdf')
            ->getInternalGenerator();

        $generator->setOptions(
                array(
                    'footer-left' => '[title]',
                    'footer-right' => '[page]/[topage]',
                    'footer-line' => true,
                    'footer-font-name' => '"Helvetica Neue",​Helvetica,​Arial,​sans-serif',
                    'footer-font-size' => 10,
                )
            );

        return new Response(
            $generator->getOutputFromHtml($html),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'attachment; filename="' . $order->getNumber() . '.pdf"'
            )
        );
    }

    /**
     * @return OrderRepository
     */
    private function getOrderRepository()
    {
        return $this->get('sylius.repository.order');
    }

    /**
     * Finds order or throws 404
     *
     * @param $number
     * @return Order
     * @throws NotFoundHttpException
     */
    private function findOrderOr404($number)
    {
        if (null === $order = $this->getOrderRepository()->findOneByNumber($number)) {
            throw $this->createNotFoundException('The order does not exist');
        }

        return $order;
    }

    /**
     * Accesses order or throws 403
     *
     * @param Order $order
     * @throws AccessDeniedException
     */
    private function accessOrderOr403(Order $order)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_SYLIUS_ADMIN') &&
            $this->getUser()->getId() !== $order->getUser()->getId()) {
            throw new AccessDeniedException();
        }

        return;
    }

}
