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

use Sylius\Bundle\FlowBundle\Process\Context\ProcessContextInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Payment\Model\PaymentInterface;

class DummyPurchaseStep extends AbstractPurchaseStep
{
    /**
     * {@inheritDoc}
     */
    protected function initializePurchase(OrderInterface $order, ProcessContextInterface $context)
    {
        return $this->render('SyliusWebBundle:Frontend/Checkout/Step:dummy_purchase.html.twig', array(
            'order'   => $order,
            'context' => $context,
        ));
    }

    /**
     * {@inheritDoc}
     */
    protected function finalizePurchase(OrderInterface $order, ProcessContextInterface $context)
    {
        $payment = $order->getPayments()->last();

        $payment->setState(PaymentInterface::STATE_COMPLETED);
        $payment->setDetails(array(
            'description' => 'The payment was done by DummyPurchaseStep from the CoreBundle. It simply change payment state to completed. Consider changing it with real payment gateway.'
        ));
    }
}
