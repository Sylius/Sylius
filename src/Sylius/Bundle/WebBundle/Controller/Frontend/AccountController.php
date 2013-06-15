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
     * Renders an invoice as PDF
     *
     * @param $number
     * @return Response
     * @throws NotFoundHttpException
     * @throws AccessDeniedException
     */
    public function orderInvoiceAction($number)
    {
        if (null === $order = $this->get('sylius.repository.order')->findOneByNumber($number)) {
            throw $this->createNotFoundException('The order does not exist');
        }

        if ($order->getUser()->getId() !== $this->getUser()->getId() &&
            false === $this->get('security.context')->isGranted('ROLE_SYLIUS_ADMIN')) {
            throw new AccessDeniedException();
        }

        $html = $this->renderView('SyliusWebBundle:Frontend/Account:Order/invoice.html.twig', array(
            'order'  => $order
        ));

        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'attachment; filename="' . $order->getNumber() . '.pdf"'
            )
        );
    }

}
