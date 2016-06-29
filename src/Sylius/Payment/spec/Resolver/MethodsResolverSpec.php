<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Payment\Resolver;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Payment\Model\PaymentInterface;
use Sylius\Payment\Model\PaymentMethodInterface;
use Sylius\Payment\Resolver\MethodsResolverInterface;
use Sylius\Resource\Repository\RepositoryInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class MethodsResolverSpec extends ObjectBehavior
{
    function let(RepositoryInterface $methodRepository)
    {
        $this->beConstructedWith($methodRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Payment\Resolver\MethodsResolver');
    }

    function it_implements_methods_resolver_interface()
    {
        $this->shouldImplement(MethodsResolverInterface::class);
    }

    function it_returns_all_methods_enabled_for_given_payment(
        RepositoryInterface $methodRepository,
        PaymentInterface $payment,
        PaymentMethodInterface $method1,
        PaymentMethodInterface $method2
    ) {
        $methodRepository->findBy(['enabled' => true])->willReturn([$method1, $method2]);

        $this->getSupportedMethods($payment)->shouldReturn([$method1, $method2]);
    }

    function it_supports_every_payment(PaymentInterface $payment)
    {
        $this->supports($payment)->shouldReturn(true);
    }
}
