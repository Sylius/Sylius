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

namespace spec\Sylius\Component\Shipping\Resolver;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Registry\PrioritizedServiceRegistryInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Model\ShippingSubjectInterface;
use Sylius\Component\Shipping\Resolver\ShippingMethodsResolverInterface;

final class CompositeMethodsResolverSpec extends ObjectBehavior
{
    public function let(PrioritizedServiceRegistryInterface $resolversRegistry): void
    {
        $this->beConstructedWith($resolversRegistry);
    }

    public function it_implements_methods_resolver_interface(): void
    {
        $this->shouldImplement(ShippingMethodsResolverInterface::class);
    }

    public function it_uses_registry_to_provide_shipping_methods_for_shipping_subject(
        ShippingMethodsResolverInterface $firstMethodsResolver,
        ShippingMethodsResolverInterface $secondMethodsResolver,
        PrioritizedServiceRegistryInterface $resolversRegistry,
        ShippingMethodInterface $shippingMethod,
        ShippingSubjectInterface $shippingSubject
    ): void {
        $resolversRegistry->all()->willReturn([$firstMethodsResolver, $secondMethodsResolver]);

        $firstMethodsResolver->supports($shippingSubject)->willReturn(false);
        $secondMethodsResolver->supports($shippingSubject)->willReturn(true);

        $secondMethodsResolver->getSupportedMethods($shippingSubject)->willReturn([$shippingMethod]);

        $this->getSupportedMethods($shippingSubject)->shouldReturn([$shippingMethod]);
    }

    public function it_returns_empty_array_if_none_of_registered_resolvers_support_passed_shipping_subject(
        ShippingMethodsResolverInterface $firstMethodsResolver,
        ShippingMethodsResolverInterface $secondMethodsResolver,
        PrioritizedServiceRegistryInterface $resolversRegistry,
        ShippingSubjectInterface $shippingSubject
    ): void {
        $resolversRegistry->all()->willReturn([$firstMethodsResolver, $secondMethodsResolver]);

        $firstMethodsResolver->supports($shippingSubject)->willReturn(false);
        $secondMethodsResolver->supports($shippingSubject)->willReturn(false);

        $this->getSupportedMethods($shippingSubject)->shouldReturn([]);
    }

    public function it_supports_subject_if_any_resolver_from_registry_supports_it(
        ShippingMethodsResolverInterface $firstMethodsResolver,
        ShippingMethodsResolverInterface $secondMethodsResolver,
        PrioritizedServiceRegistryInterface $resolversRegistry,
        ShippingSubjectInterface $shippingSubject
    ): void {
        $resolversRegistry->all()->willReturn([$firstMethodsResolver, $secondMethodsResolver]);

        $firstMethodsResolver->supports($shippingSubject)->willReturn(false);
        $firstMethodsResolver->supports($shippingSubject)->willReturn(true);

        $this->supports($shippingSubject)->shouldReturn(true);
    }

    public function it_does_not_support_subject_if_none_of_resolvers_from_registry_supports_it(
        ShippingMethodsResolverInterface $firstMethodsResolver,
        ShippingMethodsResolverInterface $secondMethodsResolver,
        PrioritizedServiceRegistryInterface $resolversRegistry,
        ShippingSubjectInterface $shippingSubject
    ): void {
        $resolversRegistry->all()->willReturn([$firstMethodsResolver, $secondMethodsResolver]);

        $firstMethodsResolver->supports($shippingSubject)->willReturn(false);
        $secondMethodsResolver->supports($shippingSubject)->willReturn(false);

        $this->supports($shippingSubject)->shouldReturn(false);
    }
}
