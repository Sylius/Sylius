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

namespace spec\Sylius\Bundle\ApiBundle\DataProvider;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Shipping\Resolver\ShippingMethodsResolverInterface;
use Symfony\Component\HttpFoundation\Request;

final class CartShippingMethodsCollectionDataProviderSpec extends ObjectBehavior
{
    function let(
        OrderRepositoryInterface $orderRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        ShippingMethodRepositoryInterface $shippingMethodsRepository,
        ShippingMethodsResolverInterface $shippingMethodsResolver,
        ChannelContextInterface $channelContext
    ): void {
        $this->beConstructedWith($orderRepository, $shipmentRepository, $shippingMethodsRepository, $shippingMethodsResolver, $channelContext);
    }

    function it_supports_shipping(): void
    {
        $this
            ->supports(
                ShippingMethodInterface::class,
                Request::METHOD_GET,
                ['filters' => ['tokenValue' => '666', 'shipmentId' => '999']],
            )
            ->shouldReturn(true)
        ;
    }

    function it_doesnt_throw_the_exception_if_cart_doesnt_exists(
        OrderRepositoryInterface $orderRepository,
        ChannelInterface $channel,
        ChannelContextInterface $channelContext
    ): void {
        $channelContext->getChannel()->willReturn($channel);
        $orderRepository->findCartByTokenValueAndChannel('666', $channel)->willReturn(null);

        $this
            ->shouldNotThrow(\InvalidArgumentException::class)
            ->during('getCollection', [
                ShippingMethodInterface::class,
                Request::METHOD_GET,
                ['filters' => ['tokenValue' => '666', 'shipmentId' => '999']],
            ])
        ;
    }

    function it_doesnt_throw_an_exception_if_shipment_doesnt_exist(
        OrderRepositoryInterface $orderRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        OrderInterface $cart,
        ChannelInterface $channel,
        ChannelContextInterface $channelContext
    ): void {
        $channelContext->getChannel()->willReturn($channel);
        $orderRepository->findCartByTokenValueAndChannel('666', $channel)->willReturn($cart);
        $cart->getId()->willReturn('111');
        $shipmentRepository->findOneByOrderId('999', '111')->willReturn(null);

        $this
            ->shouldNotThrow(\InvalidArgumentException::class)
            ->during('getCollection', [
                ShippingMethodInterface::class,
                Request::METHOD_GET,
                ['filters' => ['tokenValue' => '666', 'shipmentId' => '999']],
            ])
        ;
    }

    function it_returns_an_empty_array_if_cart_not_found(
        OrderRepositoryInterface $orderRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        OrderInterface $cart,
        ShipmentInterface $shipment,
        ChannelContextInterface $channelContext,
        ShippingMethodsResolverInterface $shippingMethodsResolver,
        ChannelInterface $channel
    ): void {
        $channelContext->getChannel()->willReturn($channel);

        $orderRepository->findCartByTokenValueAndChannel('666', $channel)->willReturn(null);
        $shipmentRepository->findOneByOrderId('999', null)->willReturn($shipment);

        $cart->hasShipment($shipment)->willReturn(false);
        $shippingMethodsResolver->getSupportedMethods($shipment)->willReturn([]);

        $this
            ->getCollection(
                ShippingMethodInterface::class,
                Request::METHOD_GET,
                [
                    'filters' => ['tokenValue' => '666', 'shipmentId' => '999'],
                ],
            )
            ->shouldReturn([])
        ;
    }

    function it_returns_an_empty_array_if_shipping_not_found(
        OrderRepositoryInterface $orderRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        OrderInterface $cart,
        ShipmentInterface $shipment,
        ChannelContextInterface $channelContext,
        ShippingMethodsResolverInterface $shippingMethodsResolver,
        ChannelInterface $channel
    ): void {
        $channelContext->getChannel()->willReturn($channel);

        $orderRepository->findCartByTokenValueAndChannel('666', $channel)->willReturn($cart);
        $cart->getId()->willReturn('111');
        $shipmentRepository->findOneByOrderId('999', '111')->willReturn(null);

        $cart->hasShipment($shipment)->willReturn(false);
        $shippingMethodsResolver->getSupportedMethods($shipment)->willReturn([]);

        $this
            ->getCollection(
                ShippingMethodInterface::class,
                Request::METHOD_GET,
                [
                    'filters' => ['tokenValue' => '666', 'shipmentId' => '999'],
                ],
            )
            ->shouldReturn([])
        ;
    }

    function it_returns_cart_shipping_methods(
        OrderRepositoryInterface $orderRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        ShippingMethodsResolverInterface $shippingMethodsResolver,
        OrderInterface $cart,
        ShipmentInterface $shipment,
        ShippingMethodInterface $shippingMethod,
        ChannelContextInterface $channelContext,
        ChannelInterface $channel
    ): void {
        $channelContext->getChannel()->willReturn($channel);

        $orderRepository->findCartByTokenValueAndChannel('666', $channel)->willReturn($cart);
        $cart->getId()->willReturn('111');
        $shipmentRepository->findOneByOrderId('999', '111')->willReturn($shipment);
        $cart->hasShipment($shipment)->willReturn(true);
        $cart->hasShipments()->willReturn(true);

        $shippingMethodsResolver->getSupportedMethods($shipment)->willReturn([$shippingMethod]);

        $this
            ->getCollection(
                ShippingMethodInterface::class,
                Request::METHOD_GET,
                [
                    'filters' => ['tokenValue' => '666', 'shipmentId' => '999'],
                ],
            )
            ->shouldReturn([$shippingMethod])
        ;
    }
}
