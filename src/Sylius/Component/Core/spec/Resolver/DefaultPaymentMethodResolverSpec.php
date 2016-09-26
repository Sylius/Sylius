<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Resolver;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface as CorePaymentInterface;
use Sylius\Component\Core\Resolver\DefaultPaymentMethodResolver;
use Sylius\Component\Payment\Exception\UnresolvedDefaultPaymentMethodException;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Resolver\DefaultPaymentMethodResolverInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class DefaultPaymentMethodResolverSpec extends ObjectBehavior
{
    function let(RepositoryInterface $paymentMethodRepository)
    {
        $this->beConstructedWith($paymentMethodRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(DefaultPaymentMethodResolver::class);
    }

    function it_implements_payment_method_resolver_interface()
    {
        $this->shouldImplement(DefaultPaymentMethodResolverInterface::class);
    }

    function it_throws_invalid_argument_exception_if_subject_not_implements_core_payment_interface(PaymentInterface $payment)
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('getDefaultPaymentMethod', [$payment]);
    }

    function it_throws_unresolved_default_payment_method_exception_if_there_is_no_enabled_payment_methods_in_database(
        CorePaymentInterface $payment,
        RepositoryInterface $paymentMethodRepository
    ) {
        $paymentMethodRepository->findBy(['enabled' => true])->willReturn([]);

        $this->shouldThrow(UnresolvedDefaultPaymentMethodException::class)->during('getDefaultPaymentMethod', [$payment]);
    }

    function it_throws_unresolved_default_payment_method_exception_if_channel_has_not_any_payment_method_from_enabled(
        CorePaymentInterface $payment,
        RepositoryInterface $paymentMethodRepository,
        PaymentMethodInterface $firstPaymentMethod,
        PaymentMethodInterface $secondPaymentMethod,
        ChannelInterface $channel,
        OrderInterface $order
    ) {
        $paymentMethodRepository->findBy(['enabled' => true])->willReturn([$firstPaymentMethod, $secondPaymentMethod]);
        $payment->getOrder()->willReturn($order);
        $order->getChannel()->willReturn($channel);

        $channel->hasPaymentMethod($firstPaymentMethod)->willReturn(false);
        $channel->hasPaymentMethod($secondPaymentMethod)->willReturn(false);

        $this->shouldThrow(UnresolvedDefaultPaymentMethodException::class)->during('getDefaultPaymentMethod', [$payment]);
    }

    function it_returns_first_payment_method_from_availables_which_is_enclosed_in_channel(
        CorePaymentInterface $payment,
        RepositoryInterface $paymentMethodRepository,
        PaymentMethodInterface $firstPaymentMethod,
        PaymentMethodInterface $secondPaymentMethod,
        ChannelInterface $channel,
        OrderInterface $order
    ) {
        $paymentMethodRepository->findBy(['enabled' => true])->willReturn([$firstPaymentMethod, $secondPaymentMethod]);
        $payment->getOrder()->willReturn($order);
        $order->getChannel()->willReturn($channel);

        $channel->hasPaymentMethod($firstPaymentMethod)->willReturn(false);
        $channel->hasPaymentMethod($secondPaymentMethod)->willReturn(true);

        $this->getDefaultPaymentMethod($payment)->shouldReturn($secondPaymentMethod);
    }
}
