<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Bundle\PayumBundle\Payum\Paypal\Action;

use Payum\Core\Action\PaymentAwareAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\SecuredCaptureRequest;
use Sylius\Component\Core\Model\PaymentInterface;
use Payum\Core\Security\GenericTokenFactoryInterface;

class CapturePaymentUsingExpressCheckoutAction extends PaymentAwareAction
{
    /**
     * @var GenericTokenFactoryInterface
     */
    protected $tokenFactory;

    /**
     * @param GenericTokenFactoryInterface $tokenFactory
     */
    public function __construct(GenericTokenFactoryInterface $tokenFactory)
    {
        $this->tokenFactory = $tokenFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function execute($request)
    {
        /** @var $request SecuredCaptureRequest */
        if (!$this->supports($request)) {
            throw RequestNotSupportedException::createActionNotSupported($this, $request);
        }

        /** @var $payment PaymentInterface */
        $payment = $request->getModel();
        $details = $payment->getDetails();
        $order = $payment->getOrder();

        if (empty($details)) {
            $details = array();
            $details['PAYMENTREQUEST_0_NOTIFYURL'] = $this->tokenFactory->createNotifyToken(
                $request->getToken()->getPaymentName(),
                $payment
            )->getTargetUrl();
            $details['PAYMENTREQUEST_0_INVNUM'] = $order->getNumber().'-'.$payment->getId();
            $details['PAYMENTREQUEST_0_CURRENCYCODE'] = 'USD';
            $details['PAYMENTREQUEST_0_AMT'] = round($order->getTotal() / 100, 2);
            $details['PAYMENTREQUEST_0_ITEMAMT'] = round($order->getTotal() / 100, 2);

            $m = 0;
            foreach ($order->getItems() as $item) {
                $details['L_PAYMENTREQUEST_0_AMT'.$m] = round($item->getTotal()/$item->getQuantity()/100, 2);
                $details['L_PAYMENTREQUEST_0_QTY'.$m] = $item->getQuantity();

                $m++;
            }

            if ($order->getTaxTotal() !== 0) {
                $details['L_PAYMENTREQUEST_0_NAME'.$m] = 'Tax Total';
                $details['L_PAYMENTREQUEST_0_AMT'.$m]  = round($order->getTaxTotal() / 100, 2);
                $details['L_PAYMENTREQUEST_0_QTY'.$m]  = 1;

                $m++;
            }

            if ($order->getPromotionTotal() !== 0) {
                $details['L_PAYMENTREQUEST_0_NAME'.$m] = 'Discount';
                $details['L_PAYMENTREQUEST_0_AMT'.$m]  = round($order->getPromotionTotal() / 100, 2);
                $details['L_PAYMENTREQUEST_0_QTY'.$m]  = 1;

                $m++;
            }

            if ($order->getShippingTotal() !== 0) {
                $details['L_PAYMENTREQUEST_0_NAME'.$m] = 'Shipping Total';
                $details['L_PAYMENTREQUEST_0_AMT'.$m]  = round($order->getShippingTotal() / 100, 2);
                $details['L_PAYMENTREQUEST_0_QTY'.$m]  = 1;
            }

            $payment->setDetails((array) $details);
        }

        $details = ArrayObject::ensureArrayObject($details);

        try {
            $request->setModel($details);
            $this->payment->execute($request);

            $payment->setDetails((array) $details);
            $request->setModel($payment);
        } catch (\Exception $e) {
            $payment->setDetails((array) $details);
            $request->setModel($payment);

            throw $e;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof SecuredCaptureRequest &&
            $request->getModel() instanceof PaymentInterface
        ;
    }
}
