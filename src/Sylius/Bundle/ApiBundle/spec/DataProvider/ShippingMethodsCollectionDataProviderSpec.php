<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
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
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Shipping\Resolver\ShippingMethodsResolverInterface;
use Symfony\Component\HttpFoundation\Request;

final class ShippingMethodsCollectionDataProviderSpec extends ObjectBehavior
{
    function let(
        ShipmentRepositoryInterface $shipmentRepository,
        ShippingMethodRepositoryInterface $shippingMethodsRepository,
        ShippingMethodsResolverInterface $shippingMethodsResolver,
        ChannelContextInterface $channelContext,
    ): void {
        $this->beConstructedWith($shipmentRepository, $shippingMethodsRepository, $shippingMethodsResolver, $channelContext);
    }

    function it_supports_shipping_methods_interface_and_only_shop_context(): void
    {
        $this
            ->supports(
                ShippingMethodInterface::class,
                Request::METHOD_GET,
                [
                    'collection_operation_name' => 'shop_get',
                    'filters' => ['tokenValue' => '666', 'shipmentId' => '999'],
                ],
            )
            ->shouldReturn(true)
        ;

        $this
            ->supports(
                ShippingMethodInterface::class,
                Request::METHOD_GET,
                [
                    'collection_operation_name' => 'admin_get',
                    'filters' => ['tokenValue' => '666', 'shipmentId' => '999'],
                ],
            )
            ->shouldReturn(false)
        ;

        $this
            ->supports(
                ChannelContextInterface::class,
                Request::METHOD_GET,
                [
                    'collection_operation_name' => 'shop_get',
                    'filters' => ['tokenValue' => '666', 'shipmentId' => '999'],
                ],
            )
            ->shouldReturn(false)
        ;
    }

    function it_returns_an_empty_array_if_token_not_provided(
        ShipmentRepositoryInterface $shipmentRepository,
        OrderInterface $cart,
        ShipmentInterface $shipment,
        ChannelContextInterface $channelContext,
        ShippingMethodsResolverInterface $shippingMethodsResolver,
        ChannelInterface $channel,
    ): void {
        $channelContext->getChannel()->willReturn($channel);

        $shipmentRepository->findOneByOrderTokenAndChannel('999', '', $channel)->willReturn(null);

        $cart->hasShipment($shipment)->willReturn(false);
        $shippingMethodsResolver->getSupportedMethods($shipment)->willReturn([]);

        $this
            ->getCollection(
                ShippingMethodInterface::class,
                Request::METHOD_GET,
                [
                    'filters' => ['tokenValue' => '', 'shipmentId' => '999'],
                ],
            )
            ->shouldReturn([])
        ;
    }

    function it_returns_an_empty_array_if_shipment_id_not_provided(
        ShipmentRepositoryInterface $shipmentRepository,
        OrderInterface $cart,
        ShipmentInterface $shipment,
        ChannelContextInterface $channelContext,
        ShippingMethodsResolverInterface $shippingMethodsResolver,
        ChannelInterface $channel,
    ): void {
        $channelContext->getChannel()->willReturn($channel);

        $shipmentRepository->findOneByOrderTokenAndChannel('', '666', $channel)->willReturn(null);

        $cart->hasShipment($shipment)->willReturn(false);
        $shippingMethodsResolver->getSupportedMethods($shipment)->willReturn([]);

        $this
            ->getCollection(
                ShippingMethodInterface::class,
                Request::METHOD_GET,
                [
                    'filters' => ['tokenValue' => '666', 'shipmentId' => ''],
                ],
            )
            ->shouldReturn([])
        ;
    }

    function it_returns_shipping_methods_resolved_based_on_given_shipment_and_order(
        ShipmentRepositoryInterface $shipmentRepository,
        ShippingMethodsResolverInterface $shippingMethodsResolver,
        OrderInterface $cart,
        ShipmentInterface $shipment,
        ShippingMethodInterface $shippingMethod,
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
    ): void {
        $channelContext->getChannel()->willReturn($channel);

        $shipmentRepository->findOneByOrderTokenAndChannel('999', '666', $channel)->willReturn($shipment);

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

    function it_returns_all_shipping_methods_for_channel_if_no_parameters_provided(
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
        ShippingMethodRepositoryInterface $shippingMethodsRepository,
        ShippingMethodInterface $shippingMethod,
    ): void {
        $channelContext->getChannel()->willReturn($channel);

        $shippingMethodsRepository->findEnabledForChannel($channel)->willReturn([$shippingMethod]);

        $this
            ->getCollection(
                ShippingMethodInterface::class,
                Request::METHOD_GET,
            )
            ->shouldReturn([$shippingMethod])
        ;
    }
}
