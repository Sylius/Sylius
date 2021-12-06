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
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Sylius\Component\Shipping\Resolver\ShippingMethodsResolverInterface;
use Symfony\Component\HttpFoundation\Request;

final class CartShippingMethodsSubresourceDataProviderSpec extends ObjectBehavior
{
    function let(
        OrderRepositoryInterface $orderRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        ShippingMethodsResolverInterface $shippingMethodsResolver
    ): void {
        $this->beConstructedWith($orderRepository, $shipmentRepository, $shippingMethodsResolver);
    }

    function it_supports_only_cart_shipping_methods_subresource_data_provider_with_id_and_shipments_subresource_identifiers(): void
    {
        $this
            ->supports(ProductInterface::class, Request::METHOD_GET)
            ->shouldReturn(false)
        ;
        $this
            ->supports(ShippingMethodInterface::class, Request::METHOD_GET)
            ->shouldReturn(false)
        ;

        $this
            ->supports(
                ShippingMethodInterface::class,
                Request::METHOD_GET,
                ['subresource_identifiers' => ['tokenValue' => '666', 'shipments' => '999']],
            )
            ->shouldReturn(true)
        ;
    }

    function it_throws_an_exception_if_cart_does_not_exist(
        OrderRepositoryInterface $orderRepository
    ): void {
        $orderRepository->findCartByTokenValue('666')->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('getSubresource', [
                ShippingMethodInterface::class,
                [],
                ['subresource_identifiers' => ['tokenValue' => '666', 'shipments' => '999']],
                Request::METHOD_GET,
            ])
        ;
    }

    function it_throws_an_exception_if_shipment_does_not_exist(
        OrderRepositoryInterface $orderRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        OrderInterface $cart
    ): void {
        $orderRepository->findCartByTokenValue('666')->willReturn($cart);
        $shipmentRepository->find('999')->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('getSubresource', [
                ShippingMethodInterface::class,
                [],
                ['subresource_identifiers' => ['tokenValue' => '666', 'shipments' => '999']],
                Request::METHOD_GET,
            ])
        ;
    }

    function it_throws_an_exception_if_shipment_does_not_match_for_order(
        OrderRepositoryInterface $orderRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        OrderInterface $cart,
        ShipmentInterface $shipment
    ): void {
        $orderRepository->findCartByTokenValue('666')->willReturn($cart);
        $shipmentRepository->find('999')->willReturn($shipment);

        $cart->hasShipment($shipment)->willReturn(false);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('getSubresource', [
                ShippingMethodInterface::class,
                [],
                ['subresource_identifiers' => ['tokenValue' => '666', 'shipments' => '999']],
                Request::METHOD_GET,
            ])
        ;
    }

    function it_returns_cart_shipping_methods(
        OrderRepositoryInterface $orderRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        ShippingMethodsResolverInterface $shippingMethodsResolver,
        OrderInterface $cart,
        ShipmentInterface $shipment,
        ShippingMethodInterface $shippingMethod
    ): void {
        $orderRepository->findCartByTokenValue('666')->willReturn($cart);
        $shipmentRepository->find('999')->willReturn($shipment);

        $cart->hasShipment($shipment)->willReturn(true);
        $cart->hasShipments()->willReturn(true);

        $shippingMethodsResolver->getSupportedMethods($shipment)->willReturn([$shippingMethod]);

        $this
            ->getSubresource(
                ShippingMethodInterface::class,
                [],
                ['subresource_identifiers' => ['tokenValue' => '666', 'shipments' => '999']],
                Request::METHOD_GET
            )
            ->shouldReturn([$shippingMethod])
        ;
    }
}
