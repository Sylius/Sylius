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
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Security\TokenInterface;
use Sylius\Bundle\PayumBundle\Payum\Action\AbstractCapturePaymentAction;
use Sylius\Component\Payment\Model\PaymentInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class CapturePaymentUsingBe2billFormAction extends AbstractCapturePaymentAction
{
    /**
     * @var Request
     */
    protected $httpRequest;

    /**
     * Define the Symfony Request
     *
     * @param Request $request
     */
    public function setRequest(Request $request = null)
    {
        $this->httpRequest = $request;
    }

    /**
     * {@inheritDoc}
     */
    public function execute($request)
    {
        $this->httpRequest->getSession()->set('payum_token', $request->getToken()->getHash());

        parent::execute($request);
    }

    /**
     * @param PaymentInterface $payment
     * @param TokenInterface   $token
     *
     * @throws LogicException
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
        $details['CLIENTEMAIL'] = $order->getEmail();
        $details['HIDECLIENTEMAIL'] = 'yes';
        $details['CLIENTUSERAGENT'] = $httpRequest->userAgent ?: 'Unknown';
        $details['CLIENTIP'] = $httpRequest->clientIp;
        $details['CLIENTIDENT'] = $order->getUser() ? $order->getUser()->getId() : $order->getEmail();
        $details['DESCRIPTION'] = sprintf('Order containing %d items for a total of %01.2f', $order->getItems()->count(), $order->getTotal() / 100);
        $details['ORDERID'] = $payment->getId();

        $payment->setDetails($details);
    }
}
