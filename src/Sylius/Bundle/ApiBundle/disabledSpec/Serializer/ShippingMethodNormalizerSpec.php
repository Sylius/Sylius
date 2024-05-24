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

namespace spec\Sylius\Bundle\ApiBundle\Serializer;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Shipping\Calculator\CalculatorInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ShippingMethodNormalizerSpec extends ObjectBehavior
{
    public function let(
        OrderRepositoryInterface $orderRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        ServiceRegistryInterface $shippingCalculators,
        RequestStack $requestStack,
        ChannelContextInterface $channelContext,
    ): void {
        $this->beConstructedWith($orderRepository, $shipmentRepository, $shippingCalculators, $requestStack, $channelContext);
    }

    public function it_supports_only_shipping_methods_interface_and_shop_context(ShippingMethodInterface $shippingMethod, ChannelInterface $channel): void
    {
        $this
            ->supportsNormalization($shippingMethod, null, [
                'collection_operation_name' => 'shop_get',
                'filters' => ['tokenValue' => '666', 'shipmentId' => '999'],
            ])
            ->shouldReturn(true)
        ;

        $this
            ->supportsNormalization($shippingMethod, null, [
                'collection_operation_name' => 'admin_get',
                'filters' => ['tokenValue' => '666', 'shipmentId' => '999'],
            ])
            ->shouldReturn(false)
        ;

        $this
            ->supportsNormalization($channel, null, [
                'collection_operation_name' => 'shop_get',
                'filters' => ['tokenValue' => '666', 'shipmentId' => '999'],
            ])
            ->shouldReturn(false)
        ;
    }

    public function it_does_not_support_if_item_operation_name_is_admin_get(ShippingMethodInterface $shippingMethod): void
    {
        $this
            ->supportsNormalization($shippingMethod, null, [
                'item_operation_name' => 'admin_get',
                'filters' => ['tokenValue' => '666', 'shipmentId' => '999'],
            ])
            ->shouldReturn(false)
        ;
    }

    public function it_does_not_support_if_the_normalizer_has_been_already_called(ShippingMethodInterface $shippingMethod): void
    {
        $this
            ->supportsNormalization($shippingMethod, null, [
                'sylius_shipping_method_normalizer_already_called' => true,
                'filters' => ['tokenValue' => '666', 'shipmentId' => '999'],
            ])
            ->shouldReturn(false)
        ;
    }

    public function it_does_not_support_if_the_filters_are_not_provided(ShippingMethodInterface $shippingMethod): void
    {
        $this->supportsNormalization($shippingMethod, null, [])->shouldReturn(false);
    }

    public function it_serializes_shipping_method_if_item_operation_name_is_different_that_admin_get(
        OrderRepositoryInterface $orderRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        ServiceRegistryInterface $shippingCalculators,
        CalculatorInterface $calculator,
        NormalizerInterface $normalizer,
        OrderInterface $cart,
        ShipmentInterface $shipment,
        ShippingMethodInterface $shippingMethod,
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
        RequestStack $requestStack,
        Request $request,
    ): void {
        $requestStack->getCurrentRequest()->willReturn($request);
        $channelContext->getChannel()->willReturn($channel);

        $request->attributes = new ParameterBag(['_api_filters' => ['tokenValue' => '666', 'shipmentId' => '999']]);

        $orderRepository->findCartByTokenValueAndChannel('666', $channel)->willReturn($cart);
        $cart->getId()->willReturn('111');
        $shipmentRepository->findOneByOrderId('999', '111')->willReturn($shipment);
        $cart->hasShipment($shipment)->willReturn(true);

        $this->setNormalizer($normalizer);

        $normalizer
            ->normalize($shippingMethod, null, [
                'sylius_shipping_method_normalizer_already_called' => true,
                'filters' => ['tokenValue' => '666', 'shipmentId' => '999'],
            ])
            ->willReturn([])
        ;

        $shippingMethod->getCalculator()->willReturn('default_calculator');
        $shippingMethod->getConfiguration()->willReturn([]);

        $shippingCalculators->get('default_calculator')->willReturn($calculator);
        $calculator->calculate($shipment, [])->willReturn(1000);

        $this
            ->normalize($shippingMethod, null, [
                'filters' => ['tokenValue' => '666', 'shipmentId' => '999'],
            ])
            ->shouldReturn(['price' => 1000])
        ;
    }

    public function it_serializes_shipping_method_if_cart_and_shipping_id_provided(
        ShipmentRepositoryInterface $shipmentRepository,
        ServiceRegistryInterface $shippingCalculators,
        CalculatorInterface $calculator,
        NormalizerInterface $normalizer,
        ShipmentInterface $shipment,
        ShippingMethodInterface $shippingMethod,
        OrderInterface $cart,
        ChannelInterface $channel,
        ChannelContextInterface $channelContext,
        RequestStack $requestStack,
        Request $request,
        OrderRepositoryInterface $orderRepository,
    ): void {
        $requestStack->getCurrentRequest()->willReturn($request);
        $channelContext->getChannel()->willReturn($channel);

        $request->attributes = new ParameterBag(['_api_filters' => ['tokenValue' => '666', 'shipmentId' => '999']]);

        $orderRepository->findCartByTokenValueAndChannel('666', $channel)->willReturn($cart);
        $cart->getId()->willReturn('111');
        $shipmentRepository->findOneByOrderId('999', '111')->willReturn($shipment);

        $cart->hasShipment($shipment)->willReturn(true);

        $this->setNormalizer($normalizer);

        $normalizer
            ->normalize($shippingMethod, null, [
                'sylius_shipping_method_normalizer_already_called' => true,
                'filters' => ['tokenValue' => '666', 'shipmentId' => '999'],
            ])
            ->willReturn([])
        ;

        $shippingMethod->getCalculator()->willReturn('default_calculator');
        $shippingMethod->getConfiguration()->willReturn([]);

        $shippingCalculators->get('default_calculator')->willReturn($calculator);
        $calculator->calculate($shipment, [])->willReturn(1000);

        $this
            ->normalize($shippingMethod, null, [
                'filters' => ['tokenValue' => '666', 'shipmentId' => '999'],
            ])
            ->shouldReturn(['price' => 1000])
        ;
    }

    public function it_doesnt_serialize_if_the_normalizer_has_been_already_called(
        NormalizerInterface $normalizer,
        ShippingMethodInterface $shippingMethod,
        ShipmentRepositoryInterface $shipmentRepository,
        ShipmentInterface $shipment,
        OrderInterface $cart,
        ChannelInterface $channel,
        ChannelContextInterface $channelContext,
        RequestStack $requestStack,
        Request $request,
        OrderRepositoryInterface $orderRepository,
    ): void {
        $requestStack->getCurrentRequest()->willReturn($request);
        $channelContext->getChannel()->willReturn($channel);

        $request->attributes = new ParameterBag(['_api_filters' => ['tokenValue' => '666', 'shipmentId' => '999']]);

        $orderRepository->findCartByTokenValueAndChannel('666', $channel)->willReturn($cart);
        $cart->getId()->willReturn('111');
        $shipmentRepository->findOneByOrderId('999', '111')->willReturn($shipment);

        $cart->hasShipment($shipment)->willReturn(true);

        $this->setNormalizer($normalizer);

        $normalizer
            ->normalize($shippingMethod, null, [
                'sylius_shipping_method_normalizer_already_called' => true,
                'filters' => ['tokenValue' => '666', 'shipmentId' => '999'],
            ])
            ->shouldNotBeCalled()
        ;
    }
}
