<?php

/*
 * This file is a part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\PayumBundle\Action\Paypal\ExpressCheckout;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\Convert;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\PayumBundle\Action\Paypal\ExpressCheckout\ConvertPaymentAction;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Payment\InvoiceNumberGeneratorInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class ConvertPaymentActionSpec extends ObjectBehavior
{
    function let(
        InvoiceNumberGeneratorInterface $invoiceNumberGenerator
    ) {
        $this->beConstructedWith($invoiceNumberGenerator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ConvertPaymentAction::class);
    }

    function it_implements_action_interface()
    {
        $this->shouldImplement(ActionInterface::class);
    }

    function it_executes_request(
        InvoiceNumberGeneratorInterface $invoiceNumberGenerator,
        Convert $request,
        PaymentInterface $payment,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductVariantInterface $productVariant,
        ProductInterface $product
    ) {
        $request->getTo()->willReturn('array');

        $payment->getId()->willReturn(19);

        $order->getId()->willReturn(92);
        $order->getId()->willReturn(92);
        $order->getCurrencyCode()->willReturn('PLN');
        $order->getTotal()->willReturn(88000);
        $order->getItems()->willReturn([$orderItem]);
        $order->getAdjustmentsTotalRecursively(AdjustmentInterface::TAX_ADJUSTMENT)->willReturn(0);
        $order->getOrderPromotionTotal()->willReturn(0);
        $order->getShippingTotal()->willReturn(8000);

        $orderItem->getVariant()->willReturn($productVariant);
        $orderItem->getDiscountedUnitPrice()->willReturn(80000);
        $orderItem->getQuantity()->willReturn(1);

        $productVariant->getProduct()->willReturn($product);

        $product->getName()->willReturn('Lamborghini Aventador Model');

        $request->getSource()->willReturn($payment);
        $payment->getOrder()->willReturn($order);

        $invoiceNumberGenerator->generate($order, $payment)->willReturn('19-92');
        $details = [
            'PAYMENTREQUEST_0_INVNUM' => '19-92',
            'PAYMENTREQUEST_0_CURRENCYCODE' => 'PLN',
            'PAYMENTREQUEST_0_AMT' => 880.00,
            'PAYMENTREQUEST_0_ITEMAMT' => 880.00,
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

    function it_throws_exception_when_source_is_not_a_payment_interface(Convert $request)
    {
        $request->getSource()->willReturn(null);

        $this
            ->shouldThrow(RequestNotSupportedException::class)
            ->during('execute', [$request])
        ;
    }
}
