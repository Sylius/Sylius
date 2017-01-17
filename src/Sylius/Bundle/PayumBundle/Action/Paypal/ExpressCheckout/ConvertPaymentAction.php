<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PayumBundle\Action\Paypal\ExpressCheckout;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\Convert;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Payment\InvoiceNumberGeneratorInterface;

final class ConvertPaymentAction implements ActionInterface
{
    /**
     * @var InvoiceNumberGeneratorInterface
     */
    private $invoiceNumberGenerator;

    /**
     * @param InvoiceNumberGeneratorInterface $invoiceNumberGenerator
     */
    public function __construct(
        InvoiceNumberGeneratorInterface $invoiceNumberGenerator
    ) {
        $this->invoiceNumberGenerator = $invoiceNumberGenerator;
    }

    /**
     * {@inheritdoc}
     *
     * @param Convert $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var PaymentInterface $payment */
        $payment = $request->getSource();
        /** @var OrderInterface $order */
        $order = $payment->getOrder();

        $details = [];
        $details['PAYMENTREQUEST_0_INVNUM'] = $this->invoiceNumberGenerator->generate($order, $payment);
        $details['PAYMENTREQUEST_0_CURRENCYCODE'] = $order->getCurrencyCode();
        $details['PAYMENTREQUEST_0_AMT'] = $this->formatPrice($order->getTotal());
        $details['PAYMENTREQUEST_0_ITEMAMT'] = $this->formatPrice($order->getTotal());

        $m = 0;
        foreach ($order->getItems() as $item) {
            $details['L_PAYMENTREQUEST_0_NAME'.$m] = $item->getVariant()->getProduct()->getName();
            $details['L_PAYMENTREQUEST_0_AMT'.$m] = $this->formatPrice($item->getDiscountedUnitPrice());
            $details['L_PAYMENTREQUEST_0_QTY'.$m] = $item->getQuantity();

            ++$m;
        }

        if (0 !== $taxTotal = $order->getAdjustmentsTotalRecursively(AdjustmentInterface::TAX_ADJUSTMENT)) {
            $details['L_PAYMENTREQUEST_0_NAME'.$m] = 'Tax Total';
            $details['L_PAYMENTREQUEST_0_AMT'.$m] = $this->formatPrice($taxTotal);
            $details['L_PAYMENTREQUEST_0_QTY'.$m] = 1;

            ++$m;
        }

        if (0 !== $promotionTotal = $order->getOrderPromotionTotal()) {
            $details['L_PAYMENTREQUEST_0_NAME'.$m] = 'Discount';
            $details['L_PAYMENTREQUEST_0_AMT'.$m] = $this->formatPrice($promotionTotal);
            $details['L_PAYMENTREQUEST_0_QTY'.$m] = 1;

            ++$m;
        }

        if (0 !== $shippingTotal = $order->getShippingTotal()) {
            $details['L_PAYMENTREQUEST_0_NAME'.$m] = 'Shipping Total';
            $details['L_PAYMENTREQUEST_0_AMT'.$m] = $this->formatPrice($shippingTotal);
            $details['L_PAYMENTREQUEST_0_QTY'.$m] = 1;
        }

        $request->setResult($details);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Convert &&
            $request->getSource() instanceof PaymentInterface &&
            $request->getTo() === 'array'
        ;
    }

    /**
     * @param int $price
     * @param string $currencyCode
     *
     * @return float
     */
    private function formatPrice($price)
    {
        return round($price / 100, 2);
    }
}
