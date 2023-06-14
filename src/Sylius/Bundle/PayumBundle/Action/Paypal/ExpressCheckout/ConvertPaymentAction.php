<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

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
    public function __construct(private InvoiceNumberGeneratorInterface $invoiceNumberGenerator)
    {
    }

    /**
     * @param Convert $request
     */
    public function execute($request): void
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

        $details = $this->prepareAddressData($order, $details);

        $m = 0;
        foreach ($order->getItems() as $item) {
            $details['L_PAYMENTREQUEST_0_NAME' . $m] = $item->getVariant()->getProduct()->getName();
            $details['L_PAYMENTREQUEST_0_AMT' . $m] = $this->formatPrice($item->getUnitPrice());
            $details['L_PAYMENTREQUEST_0_QTY' . $m] = $item->getQuantity();

            ++$m;
        }

        if (0 !== $taxTotal = $order->getAdjustmentsTotalRecursively(AdjustmentInterface::TAX_ADJUSTMENT)) {
            $details['L_PAYMENTREQUEST_0_NAME' . $m] = 'Tax Total';
            $details['L_PAYMENTREQUEST_0_AMT' . $m] = $this->formatPrice($taxTotal);
            $details['L_PAYMENTREQUEST_0_QTY' . $m] = 1;

            ++$m;
        }

        if (0 !== $promotionTotal = $order->getOrderPromotionTotal()) {
            $details['L_PAYMENTREQUEST_0_NAME' . $m] = 'Discount';
            $details['L_PAYMENTREQUEST_0_AMT' . $m] = $this->formatPrice($promotionTotal);
            $details['L_PAYMENTREQUEST_0_QTY' . $m] = 1;

            ++$m;
        }

        if (0 !== $shippingTotal = $this->getShippingTotalWithoutTaxes($order)) {
            $details['L_PAYMENTREQUEST_0_NAME' . $m] = 'Shipping Total';
            $details['L_PAYMENTREQUEST_0_AMT' . $m] = $this->formatPrice($shippingTotal);
            $details['L_PAYMENTREQUEST_0_QTY' . $m] = 1;
        }

        $request->setResult($details);
    }

    public function supports($request): bool
    {
        return
            $request instanceof Convert &&
            $request->getSource() instanceof PaymentInterface &&
            $request->getTo() === 'array'
        ;
    }

    private function getShippingTotalWithoutTaxes(OrderInterface $order): int
    {
        return $order->getAdjustmentsTotal(AdjustmentInterface::SHIPPING_ADJUSTMENT) + $order->getAdjustmentsTotal(AdjustmentInterface::ORDER_SHIPPING_PROMOTION_ADJUSTMENT);
    }

    private function formatPrice(int $price): float
    {
        return round($price / 100, 2);
    }

    private function prepareAddressData(OrderInterface $order, array $details): array
    {
        $details['EMAIL'] = $order->getCustomer()->getEmail();
        $billingAddress = $order->getBillingAddress();
        $details['LOCALECODE'] = $billingAddress->getCountryCode();
        $details['PAYMENTREQUEST_0_SHIPTONAME'] = $billingAddress->getFullName();
        $details['PAYMENTREQUEST_0_SHIPTOSTREET'] = $billingAddress->getStreet();
        $details['PAYMENTREQUEST_0_SHIPTOCITY'] = $billingAddress->getCity();
        $details['PAYMENTREQUEST_0_SHIPTOZIP'] = $billingAddress->getPostcode();
        $details['PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE'] = $billingAddress->getCountryCode();

        if ($billingAddress->getPhoneNumber() !== null) {
            $details['PAYMENTREQUEST_0_SHIPTOPHONENUM'] = $billingAddress->getPhoneNumber();
        }

        $province = $billingAddress->getProvinceCode() ?? $billingAddress->getProvinceName();
        if ($province !== null) {
            $details['PAYMENTREQUEST_0_SHIPTOSTATE'] = $province;
        }

        return $details;
    }
}
