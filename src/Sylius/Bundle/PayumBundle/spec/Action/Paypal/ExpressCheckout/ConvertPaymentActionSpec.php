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

namespace spec\Sylius\Bundle\PayumBundle\Action\Paypal\ExpressCheckout;

use Doctrine\Common\Collections\ArrayCollection;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\Convert;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Payment\InvoiceNumberGeneratorInterface;

final class ConvertPaymentActionSpec extends ObjectBehavior
{
    function let(InvoiceNumberGeneratorInterface $invoiceNumberGenerator): void
    {
        $this->beConstructedWith($invoiceNumberGenerator);
    }

    function it_implements_action_interface(): void
    {
        $this->shouldImplement(ActionInterface::class);
    }

    function it_executes_request_taking_one_tax_adjustment_to_account(
        InvoiceNumberGeneratorInterface $invoiceNumberGenerator,
        Convert $request,
        PaymentInterface $payment,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductVariantInterface $productVariant,
        ProductInterface $product,
        CustomerInterface $customer,
        AddressInterface $billingAddress,
    ): void {
        $request->getTo()->willReturn('array');

        $payment->getId()->willReturn(19);

        $order->getId()->willReturn(92);
        $order->getId()->willReturn(92);
        $order->getCurrencyCode()->willReturn('PLN');
        $order->getTotal()->willReturn(88000);
        $order->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));
        $order->getAdjustmentsTotalRecursively(AdjustmentInterface::TAX_ADJUSTMENT)->willReturn(0);
        $order->getAdjustmentsTotal(AdjustmentInterface::SHIPPING_ADJUSTMENT)->willReturn(8000);
        $order->getAdjustmentsTotal(AdjustmentInterface::ORDER_SHIPPING_PROMOTION_ADJUSTMENT)->willReturn(0);
        $order->getOrderPromotionTotal()->willReturn(0);
        $order->getShippingTotal()->shouldNotBeCalled();

        $orderItem->getVariant()->willReturn($productVariant);
        $orderItem->getUnitPrice()->willReturn(80000);
        $orderItem->getQuantity()->willReturn(1);

        $productVariant->getProduct()->willReturn($product);

        $product->getName()->willReturn('Lamborghini Aventador Model');

        $order->getCustomer()->willReturn($customer);
        $customer->getEmail()->willReturn('john@doe.com');

        $order->getBillingAddress()->willReturn($billingAddress);
        $billingAddress->getCountryCode()->willReturn('US');
        $billingAddress->getFullName()->willReturn('John Doe');
        $billingAddress->getStreet()->willReturn('Main St. 123');
        $billingAddress->getCity()->willReturn('New York');
        $billingAddress->getPostcode()->willReturn('20500');
        $billingAddress->getPhoneNumber()->willReturn('888222111');
        $billingAddress->getProvinceCode()->willReturn('NY');

        $request->getSource()->willReturn($payment);
        $payment->getOrder()->willReturn($order);

        $invoiceNumberGenerator->generate($order, $payment)->willReturn('19-92');
        $details = [
            'PAYMENTREQUEST_0_INVNUM' => '19-92',
            'PAYMENTREQUEST_0_CURRENCYCODE' => 'PLN',
            'PAYMENTREQUEST_0_AMT' => 880.00,
            'PAYMENTREQUEST_0_ITEMAMT' => 880.00,
            'EMAIL' => 'john@doe.com',
            'LOCALECODE' => 'US',
            'PAYMENTREQUEST_0_SHIPTONAME' => 'John Doe',
            'PAYMENTREQUEST_0_SHIPTOSTREET' => 'Main St. 123',
            'PAYMENTREQUEST_0_SHIPTOCITY' => 'New York',
            'PAYMENTREQUEST_0_SHIPTOZIP' => '20500',
            'PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE' => 'US',
            'PAYMENTREQUEST_0_SHIPTOPHONENUM' => '888222111',
            'PAYMENTREQUEST_0_SHIPTOSTATE' => 'NY',
            'L_PAYMENTREQUEST_0_NAME0' => 'Lamborghini Aventador Model',
            'L_PAYMENTREQUEST_0_AMT0' => 800.00,
            'L_PAYMENTREQUEST_0_QTY0' => 1,
            'L_PAYMENTREQUEST_0_NAME1' => 'Shipping Total',
            'L_PAYMENTREQUEST_0_AMT1' => 80.00,
            'L_PAYMENTREQUEST_0_QTY1' => 1,
        ];

        $request->setResult($details)->shouldBeCalled();

        $this->execute($request);
    }

    function it_executes_request_taking_shipping_promotion_to_account(
        InvoiceNumberGeneratorInterface $invoiceNumberGenerator,
        Convert $request,
        PaymentInterface $payment,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductVariantInterface $productVariant,
        ProductInterface $product,
        CustomerInterface $customer,
        AddressInterface $billingAddress,
    ): void {
        $request->getTo()->willReturn('array');

        $payment->getId()->willReturn(19);

        $order->getId()->willReturn(92);
        $order->getId()->willReturn(92);
        $order->getCurrencyCode()->willReturn('PLN');
        $order->getTotal()->willReturn(88000);
        $order->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));
        $order->getAdjustmentsTotalRecursively(AdjustmentInterface::TAX_ADJUSTMENT)->willReturn(0);
        $order->getAdjustmentsTotal(AdjustmentInterface::SHIPPING_ADJUSTMENT)->willReturn(8000);
        $order->getAdjustmentsTotal(AdjustmentInterface::ORDER_SHIPPING_PROMOTION_ADJUSTMENT)->willReturn(-2000);
        $order->getOrderPromotionTotal()->willReturn(0);
        $order->getShippingTotal()->shouldNotBeCalled();

        $orderItem->getVariant()->willReturn($productVariant);
        $orderItem->getUnitPrice()->willReturn(80000);
        $orderItem->getQuantity()->willReturn(1);

        $productVariant->getProduct()->willReturn($product);

        $product->getName()->willReturn('Lamborghini Aventador Model');

        $order->getCustomer()->willReturn($customer);
        $customer->getEmail()->willReturn('john@doe.com');

        $order->getBillingAddress()->willReturn($billingAddress);
        $billingAddress->getCountryCode()->willReturn('US');
        $billingAddress->getFullName()->willReturn('John Doe');
        $billingAddress->getStreet()->willReturn('Main St. 123');
        $billingAddress->getCity()->willReturn('New York');
        $billingAddress->getPostcode()->willReturn('20500');
        $billingAddress->getPhoneNumber()->willReturn('888222111');
        $billingAddress->getProvinceCode()->willReturn('NY');

        $request->getSource()->willReturn($payment);
        $payment->getOrder()->willReturn($order);

        $invoiceNumberGenerator->generate($order, $payment)->willReturn('19-92');
        $details = [
            'PAYMENTREQUEST_0_INVNUM' => '19-92',
            'PAYMENTREQUEST_0_CURRENCYCODE' => 'PLN',
            'PAYMENTREQUEST_0_AMT' => 880.00,
            'PAYMENTREQUEST_0_ITEMAMT' => 880.00,
            'EMAIL' => 'john@doe.com',
            'LOCALECODE' => 'US',
            'PAYMENTREQUEST_0_SHIPTONAME' => 'John Doe',
            'PAYMENTREQUEST_0_SHIPTOSTREET' => 'Main St. 123',
            'PAYMENTREQUEST_0_SHIPTOCITY' => 'New York',
            'PAYMENTREQUEST_0_SHIPTOZIP' => '20500',
            'PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE' => 'US',
            'PAYMENTREQUEST_0_SHIPTOPHONENUM' => '888222111',
            'PAYMENTREQUEST_0_SHIPTOSTATE' => 'NY',
            'L_PAYMENTREQUEST_0_NAME0' => 'Lamborghini Aventador Model',
            'L_PAYMENTREQUEST_0_AMT0' => 800.00,
            'L_PAYMENTREQUEST_0_QTY0' => 1,
            'L_PAYMENTREQUEST_0_NAME1' => 'Shipping Total',
            'L_PAYMENTREQUEST_0_AMT1' => 60.00,
            'L_PAYMENTREQUEST_0_QTY1' => 1,
        ];

        $request->setResult($details)->shouldBeCalled();

        $this->execute($request);
    }

    function it_throws_exception_when_source_is_not_a_payment_interface(Convert $request): void
    {
        $request->getSource()->willReturn(null);

        $this
            ->shouldThrow(RequestNotSupportedException::class)
            ->during('execute', [$request])
        ;
    }
}
