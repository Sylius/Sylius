<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Core\Resolver;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Payment\Model\PaymentInterface as BasePaymentInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Resolver\PaymentMethodsResolverInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class ChannelBasedPaymentMethodsResolverSpec extends ObjectBehavior
{
    function let(PaymentMethodRepositoryInterface $paymentMethodRepository): void
    {
        $this->beConstructedWith($paymentMethodRepository);
    }

    function it_implements_a_payment_methods_resolver_interface(): void
    {
        $this->shouldImplement(PaymentMethodsResolverInterface::class);
    }

    function it_returns_payment_methods_matched_for_order_channel(
        PaymentInterface $payment,
        OrderInterface $order,
        ChannelInterface $channel,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        PaymentMethodInterface $firstPaymentMethod,
        PaymentMethodInterface $secondPaymentMethod
    ): void {
        $payment->getOrder()->willReturn($order);
        $order->getChannel()->willReturn($channel);

        $paymentMethodRepository
            ->findEnabledForChannel($channel)
            ->willReturn([$firstPaymentMethod, $secondPaymentMethod])
        ;

        $this->getSupportedMethods($payment)->shouldReturn([$firstPaymentMethod, $secondPaymentMethod]);

    }

    function it_returns_an_empty_collection_if_there_is_no_enabled_payment_methods_for_order_channel(
        PaymentInterface $payment,
        OrderInterface $order,
        ChannelInterface $channel,
        PaymentMethodRepositoryInterface $paymentMethodRepository
    ): void {
        $payment->getOrder()->willReturn($order);
        $order->getChannel()->willReturn($channel);

        $paymentMethodRepository
            ->findEnabledForChannel($channel)
            ->willReturn([])
        ;

        $this->getSupportedMethods($payment)->shouldReturn([]);

    }

    function it_supports_shipments_with_order_and_its_shipping_address_defined(
        PaymentInterface $payment,
        OrderInterface $order,
        ChannelInterface $channel
    ): void {
        $payment->getOrder()->willReturn($order);
        $order->getChannel()->willReturn($channel);

        $this->supports($payment)->shouldReturn(true);
    }

    function it_does_not_support_payments_for_order_with_not_assigned_channel(
        PaymentInterface $payment,
        OrderInterface $order

    ): void {
        $payment->getOrder()->willReturn($order);
        $order->getChannel()->willReturn(null);

        $this->supports($payment)->shouldReturn(false);
    }

    function it_does_not_support_payment_if_payment_is_not_instance_of_core_payment_interface(BasePaymentInterface $payment): void
    {
        $this->supports($payment)->shouldReturn(false);
    }

    function it_does_not_support_payments_which_has_no_order_defined(PaymentInterface $payment): void
    {
        $payment->getOrder()->willReturn(null);

        $this->supports($payment)->shouldReturn(false);
    }
}
