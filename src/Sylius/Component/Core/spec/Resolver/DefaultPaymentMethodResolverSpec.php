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

use InvalidArgumentException;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface as CorePaymentInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Payment\Exception\UnresolvedDefaultPaymentMethodException;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Resolver\DefaultPaymentMethodResolverInterface;
use Sylius\Component\Payment\Resolver\PaymentMethodsResolverInterface;

final class DefaultPaymentMethodResolverSpec extends ObjectBehavior
{
    function let(PaymentMethodRepositoryInterface $paymentMethodRepository): void
    {
        $this->beConstructedWith($paymentMethodRepository);
    }

    function it_implements_a_payment_method_resolver_interface(): void
    {
        $this->shouldImplement(DefaultPaymentMethodResolverInterface::class);
    }

    function it_throws_an_invalid_argument_exception_if_subject_not_implements_core_payment_interface(
        PaymentInterface $payment
    ): void {
        $this->shouldThrow(InvalidArgumentException::class)->during('getDefaultPaymentMethod', [$payment]);
    }

    function it_throws_an_unresolved_default_payment_method_exception_if_there_is_no_enabled_payment_methods_in_database(
        CorePaymentInterface $payment,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        ChannelInterface $channel,
        OrderInterface $order
    ): void {
        $payment->getOrder()->willReturn($order);
        $order->getChannel()->willReturn($channel);
        $paymentMethodRepository->findEnabledForChannel($channel)->willReturn([]);

        $this
            ->shouldThrow(UnresolvedDefaultPaymentMethodException::class)
            ->during('getDefaultPaymentMethod', [$payment])
        ;
    }

    function it_returns_first_payment_method_from_availables_which_is_enclosed_in_channel(
        CorePaymentInterface $payment,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        PaymentMethodInterface $firstPaymentMethod,
        PaymentMethodInterface $secondPaymentMethod,
        ChannelInterface $channel,
        OrderInterface $order
    ): void {
        $payment->getOrder()->willReturn($order);
        $order->getChannel()->willReturn($channel);
        $paymentMethodRepository
            ->findEnabledForChannel($channel)
            ->willReturn([$firstPaymentMethod, $secondPaymentMethod])
        ;

        $this->getDefaultPaymentMethod($payment)->shouldReturn($firstPaymentMethod);
    }

    function it_returns_the_first_method_from_the_payment_methods_resolver_when_passed(
        CorePaymentInterface $payment,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        PaymentMethodInterface $firstPaymentMethod,
        PaymentMethodInterface $secondPaymentMethod,
        ChannelInterface $channel,
        OrderInterface $order,
        PaymentMethodsResolverInterface $paymentMethodsResolver
    ): void {
        $this->beConstructedWith($paymentMethodRepository, $paymentMethodsResolver);

        $payment->getOrder()->willReturn($order);
        $order->getChannel()->willReturn($channel);
        $paymentMethodRepository
            ->findEnabledForChannel($channel)
            ->willReturn([$firstPaymentMethod, $secondPaymentMethod])
        ;
        $paymentMethodsResolver->getSupportedMethods($payment)->willReturn([$secondPaymentMethod]);

        $this->getDefaultPaymentMethod($payment)->shouldReturn($secondPaymentMethod);
    }

    function it_throws_an_exception_if_neither_a_repository_nor_a_resolver_is_passed_to_the_constructor(): void
    {
        $this->beConstructedWith();

        $this->shouldThrow(InvalidArgumentException::class)->duringInstantiation();
    }
}
