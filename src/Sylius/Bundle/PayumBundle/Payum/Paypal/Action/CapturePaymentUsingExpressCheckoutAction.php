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

use Payum\Core\Security\GenericTokenFactoryInterface;
use Payum\Core\Security\TokenInterface;

use Sylius\Bundle\PayumBundle\Payum\Action\AbstractCapturePaymentAction;
use Sylius\Component\Core\Model\PaymentInterface;

class CapturePaymentUsingExpressCheckoutAction extends AbstractCapturePaymentAction
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
    protected function composeDetails(PaymentInterface $payment, TokenInterface $token)
    {
        if ($payment->getDetails()) {
            return;
        }

        $order = $payment->getOrder();

        $details = array();
        $details['PAYMENTREQUEST_0_NOTIFYURL'] = $this->tokenFactory->createNotifyToken(
            $token->getPaymentName(),
            $payment
        )->getTargetUrl();
        $details['PAYMENTREQUEST_0_INVNUM'] = $order->getNumber().'-'.$payment->getId();
        $details['PAYMENTREQUEST_0_CURRENCYCODE'] = $order->getCurrency();
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

        $payment->setDetails($details);
    }
}
