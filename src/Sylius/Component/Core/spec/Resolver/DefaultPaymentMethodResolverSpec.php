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
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Core\Resolver\DefaultPaymentMethodResolver;
use Sylius\Component\Core\Resolver\DefaultPaymentMethodResolverInterface;
use Sylius\Component\Payment\Exception\UnresolvedDefaultPaymentMethodException;
use Sylius\Component\Payment\Model\PaymentMethodInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class DefaultPaymentMethodResolverSpec extends ObjectBehavior
{
    function let(PaymentMethodRepositoryInterface $paymentMethodRepository)
    {
        $this->beConstructedWith($paymentMethodRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(DefaultPaymentMethodResolver::class);
    }

    function it_implements_a_payment_method_resolver_interface()
    {
        $this->shouldImplement(DefaultPaymentMethodResolverInterface::class);
    }

    function it_throws_an_unresolved_default_payment_method_exception_if_there_is_no_enabled_payment_methods_in_database(
        ChannelInterface $channel,
        PaymentMethodRepositoryInterface $paymentMethodRepository
    ) {
        $paymentMethodRepository->findEnabledForChannel($channel)->willReturn([]);

        $this
            ->shouldThrow(UnresolvedDefaultPaymentMethodException::class)
            ->during('getDefaultPaymentMethodByChannel', [$channel])
        ;
    }

    function it_returns_first_payment_method_from_availables_which_is_enclosed_in_channel(
        ChannelInterface $channel,
        PaymentMethodInterface $firstPaymentMethod,
        PaymentMethodInterface $secondPaymentMethod,
        PaymentMethodRepositoryInterface $paymentMethodRepository
    ) {
        $paymentMethodRepository
            ->findEnabledForChannel($channel)
            ->willReturn([$firstPaymentMethod, $secondPaymentMethod])
        ;

        $this->getDefaultPaymentMethodByChannel($channel)->shouldReturn($firstPaymentMethod);
    }
}
