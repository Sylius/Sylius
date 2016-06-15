<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Shipping\Resolver;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Registry\PrioritizedServiceRegistryInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Model\ShippingSubjectInterface;
use Sylius\Component\Shipping\Resolver\CompositeMethodsResolver;
use Sylius\Component\Shipping\Resolver\MethodsResolverInterface;

/**
 * @mixin CompositeMethodsResolver
 *
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class CompositeMethodsResolverSpec extends ObjectBehavior
{
    function let(PrioritizedServiceRegistryInterface $resolversRegistry)
    {
        $this->beConstructedWith($resolversRegistry);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Shipping\Resolver\CompositeMethodsResolver');
    }

    function it_implements_composite_methods_resolver_interface()
    {
        $this->shouldImplement(MethodsResolverInterface::class);
    }
    
    function it_uses_registry_to_provide_shipping_methods_for_shipping_subject(
        MethodsResolverInterface $firstMethodsResolver,
        MethodsResolverInterface $secondMethodsResolver,
        PrioritizedServiceRegistryInterface $resolversRegistry,
        ShippingMethodInterface $shippingMethod,
        ShippingSubjectInterface $shippingSubject
    ) {
        $resolversRegistry->all()->willReturn([$firstMethodsResolver, $secondMethodsResolver]);

        $firstMethodsResolver->supports($shippingSubject)->willReturn(false);
        $secondMethodsResolver->supports($shippingSubject)->willReturn(true);

        $secondMethodsResolver->getSupportedMethods($shippingSubject)->willReturn([$shippingMethod]);

        $this->getSupportedMethods($shippingSubject)->shouldReturn([$shippingMethod]);
    }

    function it_returns_empty_array_if_none_of_registered_resolvers_support_passed_shipping_subject(
        MethodsResolverInterface $firstMethodsResolver,
        MethodsResolverInterface $secondMethodsResolver,
        PrioritizedServiceRegistryInterface $resolversRegistry,
        ShippingSubjectInterface $shippingSubject
    ) {
        $resolversRegistry->all()->willReturn([$firstMethodsResolver, $secondMethodsResolver]);

        $firstMethodsResolver->supports($shippingSubject)->willReturn(false);
        $secondMethodsResolver->supports($shippingSubject)->willReturn(false);

        $this->getSupportedMethods($shippingSubject)->shouldReturn([]);
    }
}
