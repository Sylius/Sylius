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
use Sylius\Component\Shipping\Exception\UnresolvedDefaultShippingMethodException;
use Sylius\Component\Shipping\Model\ShipmentInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Shipping\Resolver\DefaultShippingMethodResolverInterface;

final class DefaultShippingMethodResolverSpec extends ObjectBehavior
{
    function let(ShippingMethodRepositoryInterface $shippingMethodRepository): void
    {
        $this->beConstructedWith($shippingMethodRepository);
    }

    function it_implements_default_shipping_method_resolver_interface(): void
    {
        $this->shouldImplement(DefaultShippingMethodResolverInterface::class);
    }

    function it_returns_first_enabled_shipping_method_as_default(
        ShippingMethodInterface $firstShippingMethod,
        ShippingMethodInterface $secondShippingMethod,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ShipmentInterface $shipment
    ): void {
        $shippingMethodRepository->findBy(['enabled' => true])->willReturn([$firstShippingMethod, $secondShippingMethod]);

        $this->getDefaultShippingMethod($shipment)->shouldReturn($firstShippingMethod);
    }

    function it_throws_exception_if_there_is_no_enabled_shipping_methods(
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ShipmentInterface $shipment
    ): void {
        $shippingMethodRepository->findBy(['enabled' => true])->willReturn([]);

        $this
            ->shouldThrow(UnresolvedDefaultShippingMethodException::class)
            ->during('getDefaultShippingMethod', [$shipment])
        ;
    }
}
