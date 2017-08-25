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

namespace spec\Sylius\Component\Core\Checker;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Checker\OrderPaymentMethodSelectionRequirementChecker;
use Sylius\Component\Core\Checker\OrderPaymentMethodSelectionRequirementCheckerInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Resolver\PaymentMethodsResolverInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class OrderPaymentMethodSelectionRequirementCheckerSpec extends ObjectBehavior
{
    function let(PaymentMethodsResolverInterface $paymentMethodsResolver)
    {
        $this->beConstructedWith($paymentMethodsResolver);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(OrderPaymentMethodSelectionRequirementChecker::class);
    }

    function it_implements_order_payment_necessity_checker_interface()
    {
        $this->shouldImplement(OrderPaymentMethodSelectionRequirementCheckerInterface::class);
    }

    function it_says_that_payment_method_has_to_be_selected_if_order_total_is_bigger_than_0(
        OrderInterface $order,
        ChannelInterface $channel
    ) {
        $order->getTotal()->willReturn(1000);
        $order->getChannel()->willReturn($channel);
        $channel->isSkippingPaymentStepAllowed()->willReturn(false);

        $this->isPaymentMethodSelectionRequired($order)->shouldReturn(true);
    }

    function it_says_that_payment_method_does_not_have_to_be_selected_if_order_total_is_0(OrderInterface $order)
    {
        $order->getTotal()->willReturn(0);

        $this->isPaymentMethodSelectionRequired($order)->shouldReturn(false);
    }

    function it_says_that_payment_method_has_to_be_selected_if_skipping_payment_step_is_disabled(
        OrderInterface $order,
        ChannelInterface $channel
    ) {
        $order->getTotal()->willReturn(1000);
        $order->getChannel()->willReturn($channel);

        $channel->isSkippingPaymentStepAllowed()->willReturn(false);

        $this->isPaymentMethodSelectionRequired($order)->shouldReturn(true);
    }

    function it_says_that_payment_method_does_not_have_to_be_selected_if_skipping_payment_step_is_enabled_and_there_is_only_one_payment_method_available(
        OrderInterface $order,
        ChannelInterface $channel,
        PaymentInterface $payment,
        PaymentMethodInterface $paymentMethod,
        PaymentMethodsResolverInterface $paymentMethodsResolver
    ) {
        $order->getTotal()->willReturn(1000);
        $order->getChannel()->willReturn($channel);
        $order->getPayments()->willReturn(new ArrayCollection([$payment->getWrappedObject()]));

        $paymentMethodsResolver->getSupportedMethods($payment)->willReturn([$paymentMethod]);
        $channel->isSkippingPaymentStepAllowed()->willReturn(true);

        $this->isPaymentMethodSelectionRequired($order)->shouldReturn(false);
    }

    function it_says_that_payment_method_has_to_be_selected_if_skipping_payment_step_is_enabled_and_there_are_more_than_one_payment_methods_available(
        OrderInterface $order,
        ChannelInterface $channel,
        PaymentInterface $payment,
        PaymentMethodInterface $paymentMethod1,
        PaymentMethodInterface $paymentMethod2,
        PaymentMethodsResolverInterface $paymentMethodsResolver
    ) {
        $order->getTotal()->willReturn(1000);
        $order->getChannel()->willReturn($channel);
        $order->getPayments()->willReturn(new ArrayCollection([$payment->getWrappedObject()]));

        $paymentMethodsResolver->getSupportedMethods($payment)->willReturn([$paymentMethod1, $paymentMethod2]);
        $channel->isSkippingPaymentStepAllowed()->willReturn(true);

        $this->isPaymentMethodSelectionRequired($order)->shouldReturn(true);
    }
}
