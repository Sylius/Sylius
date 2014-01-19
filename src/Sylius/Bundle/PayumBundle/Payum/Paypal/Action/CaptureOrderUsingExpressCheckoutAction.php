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

use Payum\Bundle\PayumBundle\Security\TokenFactory;
use Payum\Core\Action\PaymentAwareAction;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\SecuredCaptureRequest;
use Sylius\Bundle\CoreBundle\Model\OrderInterface;

class CaptureOrderUsingExpressCheckoutAction extends PaymentAwareAction
{
    /**
     * @var TokenFactory
     */
    protected $tokenFactory;

    /**
     * @param TokenFactory $tokenFactory
     */
    public function __construct(TokenFactory $tokenFactory)
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

        /** @var OrderInterface $order */
        $order = $request->getModel();
        $payment = $order->getPayment();

        $details = $payment->getDetails();
        if (empty($details)) {
            $details['RETURNURL'] = $request->getToken()->getTargetUrl();
            $details['CANCELURL'] = $request->getToken()->getTargetUrl();
            $details['NOTIFYURL'] = $this->tokenFactory->createNotifyToken(
                $request->getToken()->getPaymentName(),
                $order
            );
            $details['INVNUM'] = $order->getNumber();

            $details['PAYMENTREQUEST_0_CURRENCYCODE'] = $order->getCurrency();
            $details['PAYMENTREQUEST_0_AMT'] = number_format($order->getTotal() / 100, 2);
            $details['PAYMENTREQUEST_0_ITEMAMT'] = number_format($order->getItemsTotal() / 100, 2);
            $details['PAYMENTREQUEST_0_TAXAMT'] = number_format($order->getTaxTotal() / 100, 2);
            $details['PAYMENTREQUEST_0_SHIPPINGAMT'] = number_format($order->getShippingTotal() / 100, 2);

            $m = 0;
            foreach ($order->getItems() as $item) {
                $details['L_PAYMENTREQUEST_0_AMT'.$m] =  number_format($item->getTotal() / 100, 2);
                $details['L_PAYMENTREQUEST_0_QTY'.$m] =  $item->getQuantity();

                $m++;
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
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof SecuredCaptureRequest &&
            $request->getModel() instanceof OrderInterface
        ;
    }
}
