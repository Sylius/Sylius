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

use Sylius\Bundle\CoreBundle\Repository\OrderRepository;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Account order controller.
 *
 * @author Julien Janvier <j.janvier@gmail.com>
 */
class OrderController extends Controller
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
            ->findByUser($this->getUser(), array('updatedAt' => 'desc'));

        return $this->render(
            'SyliusWebBundle:Frontend/Account:Order/index.html.twig',
            array('orders' => $orders)
        );
    }

    /**
     * Get single order of the current user
     *
     * @param string $number
     *
     * @return Response
     * @throws NotFoundHttpException
     * @throws AccessDeniedException
     */
    public function showOrderAction($number)
    {
        $order = $this->findOrderOr404($number);

        return $this->render(
            'SyliusWebBundle:Frontend/Account:Order/show.html.twig',
            array('order' => $order)
        );
    }

    /**
     * Renders an invoice as PDF
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
            throw $this->createNotFoundException('The invoice can not yet be generated');
        }

        $html = $this->renderView('SyliusWebBundle:Frontend/Account:Order/invoice.html.twig', array(
            'order'  => $order
        ));

        if ('html' === $request->attributes->get('_format')) {
            return new Response($html);
        }

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
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'attachment; filename="' . $order->getNumber() . '.pdf"'
            )
        );
    }

    /**
     * @return OrderRepository
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
     * @throws NotFoundHttpException
     * @throws AccessDeniedException
     */
    protected function findOrderOr404($number)
    {
        if (null === $order = $this->getOrderRepository()->findOneByNumber($number)) {
            throw $this->createNotFoundException('The order does not exist.');
        }

        if (!$this->get('security.context')->isGranted('ROLE_SYLIUS_ADMIN') && $this->getUser()->getId() !== $order->getUser()->getId()) {
            throw new AccessDeniedException();
        }

        return $order;
    }
}
