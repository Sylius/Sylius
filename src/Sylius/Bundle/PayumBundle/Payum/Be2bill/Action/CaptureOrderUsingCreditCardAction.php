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

use Payum\Action\PaymentAwareAction;
new \Payum\Exception\LogicException;
use Payum\Exception\RequestNotSupportedException;
use Payum\Request\SecuredCaptureRequest;
use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\PayumBundle\Payum\Request\ObtainCreditCardRequest;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class CaptureOrderUsingCreditCardAction extends PaymentAwareAction
{
    protected $httpRequest;

    public function setRequest(Request $request = null)
    {
        $this->httpRequest = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($request)
    {
        /** @var $request SecuredCaptureRequest */
        if (!$this->supports($request)) {
            throw RequestNotSupportedException::createActionNotSupported($this, $request);
        }

        if (!$this->httpRequest) {
            throw new LogicException('The action can be run only when http request is set.');
        }

        /** @var OrderInterface $order */
        $order = $request->getModel();
        $payment = $order->getPayment();

        $details = $payment->getDetails();

        if (empty($details)) {
            $this->payment->execute($obtainCreditCardRequest = new ObtainCreditCardRequest($order));

            $details['AMOUNT'] = $order->getTotal();
            $details['CLIENTEMAIL'] = $order->getUser()->getEmail();
            $details['CLIENTUSERAGENT'] = $this->httpRequest->headers->get('User-Agent', 'Unknown');
            $details['CLIENTIP'] = $this->httpRequest->getClientIp();
            $details['CLIENTIDENT'] = $order->getUser()->getId();
            $details['DESCRIPTION'] = sprintf('Order containing %d items for a total of %01.2f', $order->getItems()->count(), $order->getTotal() / 100);
            $details['ORDERID'] = $order->getId();
            $details['CARDCODE'] = $obtainCreditCardRequest->getCreditCard()->getNumber();
            $details['CARDCVV'] = $obtainCreditCardRequest->getCreditCard()->getSecurityCode();
            $details['CARDFULLNAME'] = $obtainCreditCardRequest->getCreditCard()->getCardholderName();
            $details['CARDVALIDITYDATE'] = sprintf(
                    '%02d-%02d', $obtainCreditCardRequest->getCreditCard()->getExpiryMonth(), substr($obtainCreditCardRequest->getCreditCard()->getExpiryYear(), -2)
            );

            $payment->setDetails($details);
        }

        try {
            $request->setModel($payment);
            $this->payment->execute($request);

            $request->setModel($order);

            //TODO: when sensitive value object is used this would be removed. Require update to payum 0.7.
            $details = $this->sanitizePayment($payment->getDetails());
            $payment->setDetails($details);
        } catch (\Exception $e) {
            //TODO: when sensitive value object is used this would be removed. Require update to payum 0.7.
            $details = $this->sanitizePayment($payment->getDetails());
            $payment->setDetails($details);

            throw $e;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports($request)
    {
        return
            $request instanceof SecuredCaptureRequest &&
            $request->getModel() instanceof OrderInterface
        ;
    }

    /**
     * Sanitize paymentDetails array by removing all card-related data
     * @param array $details
     * @return array $details
     */
    protected function sanitizePayment(array $details)
    {
        foreach (array('CARDCODE', 'CARDCVV', 'CARDFULLNAME', 'CARDVALIDITYDATE') as $idx) {
            unset($details[$idx]);
        }

        return $details;
    }
}
