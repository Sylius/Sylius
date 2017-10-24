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

namespace spec\Sylius\Component\Payment\Resolver;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Payment\Exception\UnresolvedDefaultPaymentMethodException;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Payment\Resolver\DefaultPaymentMethodResolverInterface;

final class DefaultPaymentMethodResolverSpec extends ObjectBehavior
{
    function let(PaymentMethodRepositoryInterface $paymentMethodRepository): void
    {
        $this->beConstructedWith($paymentMethodRepository);
    }

    function it_implements_default_payment_method_resolver_interface(): void
    {
        $this->shouldImplement(DefaultPaymentMethodResolverInterface::class);
    }

    function it_returns_first_enabled_payment_method_as_default(
        PaymentMethodInterface $firstPaymentMethod,
        PaymentMethodInterface $secondPaymentMethod,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        PaymentInterface $payment
    ): void {
        $paymentMethodRepository->findBy(['enabled' => true])->willReturn([$firstPaymentMethod, $secondPaymentMethod]);

        $this->getDefaultPaymentMethod($payment)->shouldReturn($firstPaymentMethod);
    }

    function it_throws_exception_if_there_are_no_enabled_payment_methods(
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        PaymentInterface $payment
    ): void {
        $paymentMethodRepository->findBy(['enabled' => true])->willReturn([]);

        $this
            ->shouldThrow(UnresolvedDefaultPaymentMethodException::class)
            ->during('getDefaultPaymentMethod', [$payment])
        ;
    }
}
