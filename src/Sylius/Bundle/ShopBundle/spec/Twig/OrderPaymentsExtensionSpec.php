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

namespace spec\Sylius\Bundle\ShopBundle\Twig;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Payment\Resolver\PaymentMethodsResolverInterface;
use Twig\Extension\AbstractExtension;

final class OrderPaymentsExtensionSpec extends ObjectBehavior
{
    function let(PaymentMethodsResolverInterface $paymentMethodsResolver): void
    {
        $this->beConstructedWith($paymentMethodsResolver);
    }

    function it_is_a_twig_extension(): void
    {
        $this->shouldImplement(AbstractExtension::class);
    }

    function it_returns_false_if_order_has_no_new_payments(OrderInterface $order): void
    {
        $order->getPayments()->willReturn(new ArrayCollection());

        $this->allNewPaymentsCanBePaid($order)->shouldReturn(false);
    }

    function it_returns_false_when_all_new_payments_have_no_supported_methods(
        PaymentMethodsResolverInterface $paymentMethodsResolver,
        PaymentInterface $firstPayment,
        PaymentInterface $secondPayment,
        PaymentInterface $thirdPayment,
        OrderInterface $order,
    ): void {
        $firstPayment->getState()->willReturn(PaymentInterface::STATE_NEW);
        $secondPayment->getState()->willReturn(PaymentInterface::STATE_CANCELLED);
        $thirdPayment->getState()->willReturn(PaymentInterface::STATE_NEW);

        $order->getPayments()->willReturn(new ArrayCollection([
            $firstPayment->getWrappedObject(),
            $secondPayment->getWrappedObject(),
            $thirdPayment->getWrappedObject(),
        ]));

        $paymentMethodsResolver->getSupportedMethods($firstPayment)->willReturn([]);
        $paymentMethodsResolver->getSupportedMethods($secondPayment)->shouldNotBeCalled();
        $paymentMethodsResolver->getSupportedMethods($thirdPayment)->willReturn([]);

        $this->allNewPaymentsCanBePaid($order)->shouldReturn(false);
    }

    function it_returns_false_when_at_least_one_new_payment_has_no_supported_methods(
        PaymentMethodsResolverInterface $paymentMethodsResolver,
        PaymentInterface $firstPayment,
        PaymentInterface $secondPayment,
        PaymentInterface $thirdPayment,
        OrderInterface $order,
    ): void {
        $firstPayment->getState()->willReturn(PaymentInterface::STATE_NEW);
        $secondPayment->getState()->willReturn(PaymentInterface::STATE_CANCELLED);
        $thirdPayment->getState()->willReturn(PaymentInterface::STATE_NEW);

        $order->getPayments()->willReturn(new ArrayCollection([
            $firstPayment->getWrappedObject(),
            $secondPayment->getWrappedObject(),
            $thirdPayment->getWrappedObject(),
        ]));

        $paymentMethodsResolver->getSupportedMethods($firstPayment)->willReturn(['method']);
        $paymentMethodsResolver->getSupportedMethods($secondPayment)->shouldNotBeCalled();
        $paymentMethodsResolver->getSupportedMethods($thirdPayment)->willReturn([]);

        $this->allNewPaymentsCanBePaid($order)->shouldReturn(false);
    }

    function it_returns_true_when_all_new_payments_have_at_least_one_supported_method(
        PaymentMethodsResolverInterface $paymentMethodsResolver,
        PaymentInterface $firstPayment,
        PaymentInterface $secondPayment,
        PaymentInterface $thirdPayment,
        OrderInterface $order,
    ): void {
        $firstPayment->getState()->willReturn(PaymentInterface::STATE_NEW);
        $secondPayment->getState()->willReturn(PaymentInterface::STATE_CANCELLED);
        $thirdPayment->getState()->willReturn(PaymentInterface::STATE_NEW);

        $order->getPayments()->willReturn(new ArrayCollection([
            $firstPayment->getWrappedObject(),
            $secondPayment->getWrappedObject(),
            $thirdPayment->getWrappedObject(),
        ]));

        $paymentMethodsResolver->getSupportedMethods($firstPayment)->willReturn(['method', 'another_method']);
        $paymentMethodsResolver->getSupportedMethods($secondPayment)->shouldNotBeCalled();
        $paymentMethodsResolver->getSupportedMethods($thirdPayment)->willReturn(['method']);

        $this->allNewPaymentsCanBePaid($order)->shouldReturn(true);
    }
}
