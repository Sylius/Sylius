<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Payment\Resolver;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Resolver\CompositeMethodsResolver;
use Sylius\Component\Payment\Resolver\PaymentMethodsResolverInterface;
use Sylius\Component\Registry\PrioritizedServiceRegistryInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class CompositeMethodsResolverSpec extends ObjectBehavior
{
    function let(PrioritizedServiceRegistryInterface $resolversRegistry)
    {
        $this->beConstructedWith($resolversRegistry);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CompositeMethodsResolver::class);
    }

    function it_implements_Sylius_payment_methods_resolver_interface()
    {
        $this->shouldImplement(PaymentMethodsResolverInterface::class);
    }

    function it_uses_registry_to_provide_payment_methods_for_payment(
        PaymentMethodsResolverInterface $firstMethodsResolver,
        PaymentMethodsResolverInterface $secondMethodsResolver,
        PrioritizedServiceRegistryInterface $resolversRegistry,
        PaymentMethodInterface $paymentMethod,
        PaymentInterface $payment
    ) {
        $resolversRegistry->all()->willReturn([$firstMethodsResolver, $secondMethodsResolver]);

        $firstMethodsResolver->supports($payment)->willReturn(false);
        $secondMethodsResolver->supports($payment)->willReturn(true);

        $secondMethodsResolver->getSupportedMethods($payment)->willReturn([$paymentMethod]);

        $this->getSupportedMethods($payment)->shouldReturn([$paymentMethod]);
    }

    function it_returns_empty_array_if_none_of_registered_resolvers_support_passed_payment(
        PaymentMethodsResolverInterface $firstMethodsResolver,
        PaymentMethodsResolverInterface $secondMethodsResolver,
        PrioritizedServiceRegistryInterface $resolversRegistry,
        PaymentInterface $payment
    ) {
        $resolversRegistry->all()->willReturn([$firstMethodsResolver, $secondMethodsResolver]);

        $firstMethodsResolver->supports($payment)->willReturn(false);
        $secondMethodsResolver->supports($payment)->willReturn(false);

        $this->getSupportedMethods($payment)->shouldReturn([]);
    }

    function it_supports_payment_if_at_least_one_registered_resolver_supports_it(
        PaymentMethodsResolverInterface $firstMethodsResolver,
        PaymentMethodsResolverInterface $secondMethodsResolver,
        PrioritizedServiceRegistryInterface $resolversRegistry,
        PaymentInterface $payment
    ) {
        $resolversRegistry->all()->willReturn([$firstMethodsResolver, $secondMethodsResolver]);

        $firstMethodsResolver->supports($payment)->willReturn(false);
        $secondMethodsResolver->supports($payment)->willReturn(true);

        $this->supports($payment)->shouldReturn(true);
    }

    function it_does_not_support_payment_if_none_of_registered_resolvers_supports_it(
        PaymentMethodsResolverInterface $firstMethodsResolver,
        PaymentMethodsResolverInterface $secondMethodsResolver,
        PrioritizedServiceRegistryInterface $resolversRegistry,
        PaymentInterface $payment
    ) {
        $resolversRegistry->all()->willReturn([$firstMethodsResolver, $secondMethodsResolver]);

        $firstMethodsResolver->supports($payment)->willReturn(false);
        $secondMethodsResolver->supports($payment)->willReturn(false);

        $this->supports($payment)->shouldReturn(false);
    }
}
