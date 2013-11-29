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
use Payum\Bridge\Spl\ArrayObject;
use Payum\Exception\RequestNotSupportedException;
use Payum\Request\CaptureRequest;
use Payum\Request\SecuredCaptureRequest;
use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\PayumBundle\Payum\Request\ObtainCreditCardRequest;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class CaptureOrderUsingCreditCardAction extends PaymentAwareAction
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
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

        /** @var OrderInterface $order */
        $order = $request->getModel();

        $paymentDetails = $order->getPayment()->getDetails();
        if (empty($paymentDetails)) {
            $this->payment->execute($obtainCreditCardRequest = new ObtainCreditCardRequest($order));

            $paymentDetails['AMOUNT'] = $order->getTotal();
            $paymentDetails['CLIENTEMAIL'] = $order->getUser()->getEmail();
            $paymentDetails['CLIENTUSERAGENT'] = $this->container->get('request')->headers->get('User-Agent');
            $paymentDetails['CLIENTIP'] = $this->container->get('request')->getClientIp();
            $paymentDetails['CLIENTIDENT'] = $order->getUser()->getId();
            $paymentDetails['DESCRIPTION'] = sprintf('Order containing %d items for a total of %01.2f', $order->getItems()->count(), $order->getTotal() / 100);
            $paymentDetails['ORDERID'] = $order->getId();
            $paymentDetails['CARDCODE'] = $obtainCreditCardRequest->getCreditCard()->getNumber();
            $paymentDetails['CARDCVV'] = $obtainCreditCardRequest->getCreditCard()->getSecurityCode();
            $paymentDetails['CARDFULLNAME'] = $obtainCreditCardRequest->getCreditCard()->getCardholderName();
            $paymentDetails['CARDVALIDITYDATE'] = sprintf(
                    '%02d-%02d', $obtainCreditCardRequest->getCreditCard()->getExpiryMonth(), substr($obtainCreditCardRequest->getCreditCard()->getExpiryYear(), -2)
            );
        }

        // TODO: find a way to simply the next logic

        $paymentDetails = ArrayObject::ensureArrayObject($paymentDetails);

        try {
            $this->payment->execute(new CaptureRequest($paymentDetails));

            $order->getPayment()->setDetails((array) $this->sanitizePayment($paymentDetails));
        } catch (\Exception $e) {
            $order->getPayment()->setDetails((array) $this->sanitizePayment($paymentDetails));

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
     * Sanitize payementDetails array by removing all card-related data
     * @param array $paymentDetails
     * @return array $paymentDetails
     */
    protected function sanitizePayment(ArrayObject $paymentDetails)
    {
        foreach (array('CARDCODE', 'CARDCVV', 'CARDFULLNAME', 'CARDVALIDITYDATE') as $idx) {
            if (isset($paymentDetails[$idx])) {
                unset($paymentDetails[$idx]);
            }
        }

        return $paymentDetails;
    }
}
