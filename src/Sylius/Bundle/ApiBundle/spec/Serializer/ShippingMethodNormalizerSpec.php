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

namespace spec\Sylius\Bundle\ApiBundle\Serializer;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Shipping\Calculator\CalculatorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ShippingMethodNormalizerSpec extends ObjectBehavior
{
    public function let(
        OrderRepositoryInterface $orderRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        ServiceRegistryInterface $shippingCalculators
    ): void {
        $this->beConstructedWith($orderRepository, $shipmentRepository, $shippingCalculators);
    }

    public function it_supports_only_shipping_method_interface(ShippingMethodInterface $shippingMethod, OrderInterface $order): void
    {
        $this
            ->supportsNormalization($shippingMethod, null, ['subresource_identifiers' => ['tokenValue' => '666', 'shipments' => '999']])
            ->shouldReturn(true)
        ;
        $this
            ->supportsNormalization($order, null, ['subresource_identifiers' => ['tokenValue' => '666', 'shipments' => '999']])
            ->shouldReturn(false)
        ;
    }

    public function it_does_not_support_if_item_operation_name_is_admin_get(ShippingMethodInterface $shippingMethod): void
    {
        $this
            ->supportsNormalization($shippingMethod, null, [
                'item_operation_name' => 'admin_get',
                'subresource_identifiers' => ['tokenValue' => '666', 'shipments' => '999'],
            ])
            ->shouldReturn(false)
        ;
    }

    public function it_does_not_support_if_the_normalizer_has_been_already_called(ShippingMethodInterface $shippingMethod): void
    {
        $this
            ->supportsNormalization($shippingMethod, null, [
                'shipping_method_normalizer_already_called' => true,
                'subresource_identifiers' => ['tokenValue' => '666', 'shipments' => '999'],
            ])
            ->shouldReturn(false)
        ;
    }

    public function it_does_not_support_if_the_subresource_identifiers_are_not_provided(ShippingMethodInterface $shippingMethod): void
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
        ShippingMethodInterface $shippingMethod
    ): void {
        $orderRepository->findCartByTokenValue('666')->willReturn($cart);
        $shipmentRepository->find('999')->willReturn($shipment);
        $cart->hasShipment($shipment)->willReturn(true);

        $this->setNormalizer($normalizer);

        $normalizer
            ->normalize($shippingMethod, null, [
                'shipping_method_normalizer_already_called' => true,
                'subresource_identifiers' => ['tokenValue' => '666', 'shipments' => '999'],
            ])
            ->willReturn([])
        ;

        $shippingMethod->getCalculator()->willReturn('default_calculator');
        $shippingMethod->getConfiguration()->willReturn([]);

        $shippingCalculators->get('default_calculator')->willReturn($calculator);
        $calculator->calculate($shipment, [])->willReturn(1000);

        $this
            ->normalize($shippingMethod, null, [
                'subresource_identifiers' => ['tokenValue' => '666', 'shipments' => '999'],
            ])
            ->shouldReturn(['price' => 1000])
        ;
    }

    public function it_throws_an_exception_if_the_normalizer_has_been_already_called(
        NormalizerInterface $normalizer,
        ShippingMethodInterface $shippingMethod
    ): void {
        $this->setNormalizer($normalizer);

        $normalizer
            ->normalize($shippingMethod, null, [
                'shipping_method_normalizer_already_called' => true,
                'subresource_identifiers' => ['tokenValue' => '666', 'shipments' => '999'],
            ])
            ->shouldNotBeCalled()
        ;

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('normalize', [$shippingMethod, null, [
                'subresource_identifiers' => ['tokenValue' => '666', 'shipments' => '999'],
            ]])
        ;
    }
}
