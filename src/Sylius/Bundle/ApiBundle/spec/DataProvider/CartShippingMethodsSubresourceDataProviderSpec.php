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
use Sylius\Bundle\ApiBundle\View\CartShippingMethodInterface;
use Sylius\Bundle\ApiBundle\View\Factory\CartShippingMethodFactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Shipping\Calculator\CalculatorInterface;
use Sylius\Component\Shipping\Resolver\ShippingMethodsResolverInterface;
use Symfony\Component\HttpFoundation\Request;

final class CartShippingMethodsSubresourceDataProviderSpec extends ObjectBehavior
{
    function let(
        OrderRepositoryInterface $orderRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        ShippingMethodsResolverInterface $shippingMethodsResolver,
        ServiceRegistryInterface $calculators,
        CartShippingMethodFactoryInterface $cartShippingMethodFactory
    ): void {
        $this->beConstructedWith(
            $orderRepository,
            $shipmentRepository,
            $shippingMethodsResolver,
            $calculators,
            $cartShippingMethodFactory
        );
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

        $context['subresource_identifiers'] = ['tokenValue' => '666', 'shipments' => '999'];

        $this
            ->supports(
                ShippingMethodInterface::class,
                Request::METHOD_GET,
                $context
            )
            ->shouldReturn(true)
        ;
    }

    function it_throws_an_exception_if_cart_does_not_exist(
        OrderRepositoryInterface $orderRepository
    ): void {
        $context['subresource_identifiers'] = ['tokenValue' => '666', 'shipments' => '999'];

        $orderRepository->findCartByTokenValue('666')->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('getSubresource', [
                ShippingMethodInterface::class,
                [],
                $context,
                Request::METHOD_GET,
            ])
        ;
    }

    function it_throws_an_exception_if_shipment_does_not_exist(
        OrderRepositoryInterface $orderRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        OrderInterface $cart
    ): void {
        $context['subresource_identifiers'] = ['tokenValue' => '666', 'shipments' => '999'];

        $orderRepository->findCartByTokenValue('666')->willReturn($cart);
        $shipmentRepository->find('999')->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('getSubresource', [
                ShippingMethodInterface::class,
                [],
                $context,
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
        $context['subresource_identifiers'] = ['tokenValue' => '666', 'shipments' => '999'];

        $orderRepository->findCartByTokenValue('666')->willReturn($cart);
        $shipmentRepository->find('999')->willReturn($shipment);

        $cart->hasShipment($shipment)->willReturn(false);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('getSubresource', [
                ShippingMethodInterface::class,
                [],
                $context,
                Request::METHOD_GET,
            ])
        ;
    }

    function it_returns_empty_array_if_cart_does_not_have_shipments(
        OrderRepositoryInterface $orderRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        OrderInterface $cart,
        ShipmentInterface $shipment
    ): void {
        $context['subresource_identifiers'] = ['tokenValue' => '666', 'shipments' => '999'];

        $orderRepository->findCartByTokenValue('666')->willReturn($cart);
        $shipmentRepository->find('999')->willReturn($shipment);

        $cart->hasShipment($shipment)->willReturn(true);
        $cart->hasShipments()->willReturn(false);

        $this
            ->getSubresource(
                ShippingMethodInterface::class,
                [],
                $context,
                Request::METHOD_GET
            )
            ->shouldReturn([])
        ;
    }

    function it_returns_cart_shipping_methods(
        OrderRepositoryInterface $orderRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        ShippingMethodsResolverInterface $shippingMethodsResolver,
        ServiceRegistryInterface $calculators,
        CartShippingMethodFactoryInterface $cartShippingMethodFactory,
        OrderInterface $cart,
        ShipmentInterface $shipment,
        ShippingMethodInterface $shippingMethod,
        CalculatorInterface $calculator,
        CartShippingMethodInterface $cartShippingMethod
    ): void {
        $context['subresource_identifiers'] = ['tokenValue' => '666', 'shipments' => '999'];

        $orderRepository->findCartByTokenValue('666')->willReturn($cart);
        $shipmentRepository->find('999')->willReturn($shipment);

        $cart->hasShipment($shipment)->willReturn(true);
        $cart->hasShipments()->willReturn(true);

        $shippingMethodsResolver->getSupportedMethods($shipment)->willReturn([$shippingMethod]);

        $shippingMethod->getCalculator()->willReturn('default_calculator');
        $shippingMethod->getConfiguration()->willReturn([]);

        $calculators->get('default_calculator')->willReturn($calculator);
        $calculator->calculate($shipment, [])->willReturn(10);

        $shippingMethod->getCode()->willReturn('inpost_pl');

        $cartShippingMethodFactory
            ->create($shippingMethod, 10)
            ->willReturn($cartShippingMethod)
        ;

        $this
            ->getSubresource(
                ShippingMethodInterface::class,
                [],
                $context,
                Request::METHOD_GET
            )
            ->shouldReturn([$cartShippingMethod])
        ;
    }
}
