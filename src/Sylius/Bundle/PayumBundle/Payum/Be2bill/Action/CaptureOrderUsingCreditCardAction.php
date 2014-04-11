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

use Payum\Core\Action\PaymentAwareAction;
use Payum\Core\Exception\LogicException;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\SecuredCaptureRequest;
use Payum\Core\Security\SensitiveValue;
use Sylius\Bundle\PayumBundle\Payum\Request\ObtainCreditCardRequest;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class CaptureOrderUsingCreditCardAction extends PaymentAwareAction
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

        /** @var OrderInterface|PaymentAwareInterface $order */
        $order = $request->getModel();
        $payment = $order->getPayment();

        $details = $payment->getDetails();

        if (empty($details)) {
            $this->payment->execute($obtainCreditCardRequest = new ObtainCreditCardRequest($order));

            $details['AMOUNT'] = $order->getTotal();
            $details['CLIENTUSERAGENT'] = $this->httpRequest->headers->get('User-Agent', 'Unknown');
            $details['CLIENTIP'] = $this->httpRequest->getClientIp();
            $details['DESCRIPTION'] = sprintf('Order containing %d items for a total of %01.2f', $order->getItems()->count(), $order->getTotal() / 100);
            $details['ORDERID'] = $order->getId();
            $details['CARDCODE'] = new SensitiveValue($obtainCreditCardRequest->getCreditCard()->getNumber());
            $details['CARDCVV'] = new SensitiveValue($obtainCreditCardRequest->getCreditCard()->getSecurityCode());
            $details['CARDFULLNAME'] = new SensitiveValue($obtainCreditCardRequest->getCreditCard()->getCardholderName());
            $details['CARDVALIDITYDATE'] = new SensitiveValue(sprintf(
                    '%02d-%02d', $obtainCreditCardRequest->getCreditCard()->getExpiryMonth(), substr($obtainCreditCardRequest->getCreditCard()->getExpiryYear(), -2)
            ));

            if ($order instanceof UserAwareInterface) {
                $details['CLIENTEMAIL'] = $order->getUser()->getEmail();
                $details['CLIENTIDENT'] = method_exists('getId', $order->getUser()) ? $order->getUser()->getId() : uniqid();
            } else {
                $details['CLIENTEMAIL'] = 'unknown@example.com';
                $details['CLIENTIDENT'] = uniqid();
            }

            $payment->setDetails($details);
        }

        try {
            $request->setModel($payment);
            $this->payment->execute($request);

            $request->setModel($order);
        } catch (\Exception $e) {
            $request->setModel($order);

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
            $request->getModel() instanceof OrderInterface &&
            $request->getModel() instanceof PaymentAwareInterface
        ;
    }
}
