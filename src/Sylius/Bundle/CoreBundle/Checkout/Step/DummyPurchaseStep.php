<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Bundle\CoreBundle\Checkout\Step;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Payment\Model\PaymentInterface;
use Symfony\Component\HttpFoundation\Response;

class DummyPurchaseStep extends AbstractPurchaseStep
{
    /**
     * @param OrderInterface $order
     *
     * @return Response
     */
    protected function initializePurchase(OrderInterface $order)
    {
        return $this->render('SyliusWebBundle:Frontend/Checkout/Step:purchase.html.twig', array(
            'order'   => $order,
        ));
    }

    /**
     * @param OrderInterface $order
     */
    protected function finalizePurchase(OrderInterface $order)
    {
        $order->getPayment()->setDetails(array(
            'description' => 'The payment was done by PurchaseStep from CoreBundle. It simply change payment state to completed'
        ));

        $order->getPayment()->setState(PaymentInterface::STATE_COMPLETED);
    }
}
