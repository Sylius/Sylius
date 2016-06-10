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
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Model\ShipmentInterface;
use Sylius\Component\Shipping\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Shipping\Resolver\DefaultShippingMethodResolverInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class DefaultShippingMethodResolverSpec extends ObjectBehavior
{
    function let(ShippingMethodRepositoryInterface $shippingMethodRepository)
    {
        $this->beConstructedWith($shippingMethodRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Shipping\Resolver\DefaultShippingMethodResolver');
    }

    function it_implements_default_shipping_method_resolver_interface()
    {
        $this->shouldImplement(DefaultShippingMethodResolverInterface::class);
    }

    function it_returns_first_enabled_shipping_method_as_default(
        ShippingMethodInterface $firstShippingMethod,
        ShippingMethodInterface $secondShippingMethod,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ShipmentInterface $shipment
    ) {
        $shippingMethodRepository->findBy(['enabled' => true])->willReturn([$firstShippingMethod, $secondShippingMethod]);

        $this->getDefaultShippingMethod($shipment)->shouldReturn($firstShippingMethod);
    }

    function it_returns_null_if_there_is_no_enabled_shipping_methods(
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ShipmentInterface $shipment
    ) {
        $shippingMethodRepository->findBy(['enabled' => true])->willReturn([]);

        $this->getDefaultShippingMethod($shipment)->shouldReturn(null);
    }
}
