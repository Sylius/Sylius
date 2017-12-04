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
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Resolver\PaymentMethodsResolverInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class PaymentMethodsResolverSpec extends ObjectBehavior
{
    function let(RepositoryInterface $methodRepository): void
    {
        $this->beConstructedWith($methodRepository);
    }

    function it_implements_methods_resolver_interface(): void
    {
        $this->shouldImplement(PaymentMethodsResolverInterface::class);
    }

    function it_returns_all_methods_enabled_for_given_payment(
        RepositoryInterface $methodRepository,
        PaymentInterface $payment,
        PaymentMethodInterface $method1,
        PaymentMethodInterface $method2
    ): void {
        $methodRepository->findBy(['enabled' => true])->willReturn([$method1, $method2]);

        $this->getSupportedMethods($payment)->shouldReturn([$method1, $method2]);
    }

    function it_supports_every_payment(PaymentInterface $payment): void
    {
        $this->supports($payment)->shouldReturn(true);
    }
}
