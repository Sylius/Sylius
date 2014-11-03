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

use Payum\Core\Exception\LogicException;
use Payum\Core\Security\TokenInterface;
use Sylius\Bundle\PayumBundle\Payum\Action\AbstractCapturePaymentAction;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class CapturePaymentUsingCreditCardAction extends AbstractCapturePaymentAction
{
    /**
     * @var Request
     */
    protected $httpRequest;

    /**
     * @param Request $request
     */
    public function setRequest(Request $request = null)
    {
        $this->httpRequest = $request;
    }

    /**
     * {@inheritDoc}
     */
    protected function composeDetails(PaymentInterface $payment, TokenInterface $token)
    {
        if ($payment->getDetails()) {
            return;
        }

        if (!$this->httpRequest) {
            throw new LogicException('The action can be run only when http request is set.');
        }

        $order = $payment->getOrder();

        $details = array();
        $details['AMOUNT'] = $order->getTotal();
        $details['CLIENTEMAIL'] = $order->getEmail();
        $details['CLIENTUSERAGENT'] = $this->httpRequest->headers->get('User-Agent', 'Unknown');
        $details['CLIENTIP'] = $this->httpRequest->getClientIp();
        $details['CLIENTIDENT'] = $order->getUser() ? $order->getUser()->getId() : $order->getEmail();
        $details['DESCRIPTION'] = sprintf('Order containing %d items for a total of %01.2f', $order->getItems()->count(), $order->getTotal() / 100);
        $details['ORDERID'] = $payment->getId();

        $payment->setDetails($details);
    }
}
