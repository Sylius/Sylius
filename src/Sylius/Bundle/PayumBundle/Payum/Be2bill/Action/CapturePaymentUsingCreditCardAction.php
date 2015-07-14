<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Bundle\PayumBundle\Payum\Be2bill\Action;

use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Security\TokenInterface;
use Sylius\Bundle\PayumBundle\Payum\Action\AbstractCapturePaymentAction;
use Sylius\Component\Payment\Model\PaymentInterface;

/**
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class CapturePaymentUsingCreditCardAction extends AbstractCapturePaymentAction
{
    /**
     * {@inheritDoc}
     */
    protected function composeDetails(PaymentInterface $payment, TokenInterface $token)
    {
        if ($payment->getDetails()) {
            return;
        }

        $this->payment->execute($httpRequest = new GetHttpRequest());

        $order = $payment->getOrder();

        $details = array();
        $details['AMOUNT'] = $order->getTotal();
        $details['CLIENTEMAIL'] = $order->getCustomer()->getEmail();
        $details['CLIENTUSERAGENT'] = $httpRequest->userAgent ?: 'Unknown';
        $details['CLIENTIP'] = $httpRequest->clientIp;
        $details['CLIENTIDENT'] = $order->getCustomer()->getId();
        $details['DESCRIPTION'] = sprintf('Order containing %d items for a total of %01.2f', $order->getItems()->count(), $order->getTotal() / 100);
        $details['ORDERID'] = $payment->getId();

        $payment->setDetails($details);
    }
}
