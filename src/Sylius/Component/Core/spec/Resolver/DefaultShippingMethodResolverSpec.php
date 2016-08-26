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
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Exception\UnresolvedDefaultShippingMethodException;
use Sylius\Component\Shipping\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Shipping\Resolver\DefaultShippingMethodResolverInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class DefaultShippingMethodResolverSpec extends ObjectBehavior
{
    function let(ShippingMethodRepositoryInterface $shippingMethodRepository)
    {
        $this->beConstructedWith($shippingMethodRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Resolver\DefaultShippingMethodResolver');
    }

    function it_implements_default_shipping_method_resolver_interface()
    {
        $this->shouldImplement(DefaultShippingMethodResolverInterface::class);
    }

    function it_returns_first_enabled_shipping_method_from_shipment_order_channel_as_default(
        ChannelInterface $channel,
        OrderInterface $order,
        ShipmentInterface $shipment,
        ShippingMethodInterface $firstShippingMethod,
        ShippingMethodInterface $secondShippingMethod,
        ShippingMethodInterface $thirdShippingMethod,
        ShippingMethodRepositoryInterface $shippingMethodRepository
    ) {
        $shippingMethodRepository
            ->findBy(['enabled' => true])
            ->willReturn([$firstShippingMethod, $secondShippingMethod, $thirdShippingMethod])
        ;

        $shipment->getOrder()->willReturn($order);
        $order->getChannel()->willReturn($channel);

        $channel->hasShippingMethod($firstShippingMethod)->willReturn(false);
        $channel->hasShippingMethod($secondShippingMethod)->willReturn(true);
        $channel->hasShippingMethod($thirdShippingMethod)->willReturn(true);

        $this->getDefaultShippingMethod($shipment)->shouldReturn($secondShippingMethod);
    }

    function it_throws_exception_if_there_is_no_enabled_shipping_methods(
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ShipmentInterface $shipment
    ) {
        $shippingMethodRepository->findBy(['enabled' => true])->willReturn([]);

        $this
            ->shouldThrow(UnresolvedDefaultShippingMethodException::class)
            ->during('getDefaultShippingMethod', [$shipment])
        ;
    }

    function it_throws_exception_if_there_is_no_enabled_shipping_methods_for_channel(
        ChannelInterface $channel,
        OrderInterface $order,
        ShipmentInterface $shipment,
        ShippingMethodInterface $firstShippingMethod,
        ShippingMethodInterface $secondShippingMethod,
        ShippingMethodInterface $thirdShippingMethod,
        ShippingMethodRepositoryInterface $shippingMethodRepository
    ) {
        $shippingMethodRepository
            ->findBy(['enabled' => true])
            ->willReturn([$firstShippingMethod, $secondShippingMethod, $thirdShippingMethod])
        ;

        $shipment->getOrder()->willReturn($order);
        $order->getChannel()->willReturn($channel);

        $channel->hasShippingMethod($firstShippingMethod)->willReturn(false);
        $channel->hasShippingMethod($secondShippingMethod)->willReturn(false);
        $channel->hasShippingMethod($thirdShippingMethod)->willReturn(false);

        $this
            ->shouldThrow(UnresolvedDefaultShippingMethodException::class)
            ->during('getDefaultShippingMethod', [$shipment])
        ;
    }

    function it_throws_exception_if_passed_shipment_is_not_core_shipment_object(
        \Sylius\Component\Shipping\Model\ShipmentInterface $shipment
    ) {
        $this->shouldThrow(\InvalidArgumentException::class)->during('getDefaultShippingMethod', [$shipment]);
    }
}
