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

namespace spec\Sylius\Bundle\ApiBundle\Serializer\Normalizer;

use ApiPlatform\Metadata\GetCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
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
        SectionProviderInterface $sectionProvider,
        OrderRepositoryInterface $orderRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        ServiceRegistryInterface $shippingCalculators,
        RequestStack $requestStack,
        NormalizerInterface $normalizer,
    ): void {
        $this->beConstructedWith(
            $sectionProvider,
            $orderRepository,
            $shipmentRepository,
            $shippingCalculators,
            $requestStack,
        );

        $this->setNormalizer($normalizer);
    }

    public function it_supports_only_shipping_method_interface_in_shop_section_with_proper_data(
        SectionProviderInterface $sectionProvider,
        ShippingMethodInterface $shippingMethod,
    ): void {
        $sectionProvider->getSection()->willReturn(new ShopApiSection());
        $this
            ->supportsNormalization($shippingMethod, null, [
                'root_operation' => new GetCollection(uriVariables: ['tokenValue' => [], 'shipmentId' => []]),
            ])
            ->shouldReturn(true)
        ;

        $sectionProvider->getSection()->willReturn(new ShopApiSection());
        $this
            ->supportsNormalization(new \stdClass(), null, [
                'root_operation' => new GetCollection(uriVariables: ['tokenValue' => [], 'shipmentId' => []]),
            ])
            ->shouldReturn(false)
        ;

        $sectionProvider->getSection()->willReturn(new AdminApiSection());
        $this
            ->supportsNormalization($shippingMethod, null, [
                'root_operation' => new GetCollection(uriVariables: ['tokenValue' => [], 'shipmentId' => []]),
            ])
            ->shouldReturn(false)
        ;

        $sectionProvider->getSection()->willReturn(new ShopApiSection());
        $this->supportsNormalization($shippingMethod)->shouldReturn(false);

        $sectionProvider->getSection()->willReturn(new ShopApiSection());
        $this
            ->supportsNormalization($shippingMethod, null, [
                'root_operation' => new GetCollection(uriVariables: ['tokenValue' => []]),
            ])
            ->shouldReturn(false)
        ;

        $sectionProvider->getSection()->willReturn(new ShopApiSection());
        $this
            ->supportsNormalization($shippingMethod, null, [
                'root_operation' => new GetCollection(uriVariables: ['shipmentId' => []]),
            ])
            ->shouldReturn(false)
        ;
    }

    public function it_does_not_support_if_the_normalizer_has_been_already_called(
        SectionProviderInterface $sectionProvider,
        ShippingMethodInterface $shippingMethod,
    ): void {
        $sectionProvider->getSection()->willReturn(new ShopApiSection());
        $this
            ->supportsNormalization($shippingMethod, null, [
                'sylius_shipping_method_normalizer_already_called' => true,
                'root_operation' => new GetCollection(uriVariables: ['tokenValue' => [], 'shipmentId' => []]),
            ])
            ->shouldReturn(false)
        ;
    }

    public function it_adds_calculated_price_of_shipping_method(
        SectionProviderInterface $sectionProvider,
        OrderRepositoryInterface $orderRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        ServiceRegistryInterface $shippingCalculators,
        RequestStack $requestStack,
        NormalizerInterface $normalizer,
        Request $request,
        ShippingMethodInterface $shippingMethod,
        ChannelInterface $channel,
        OrderInterface $cart,
        ShipmentInterface $shipment,
        CalculatorInterface $calculator,
    ): void {
        $operation = new GetCollection(uriVariables: ['tokenValue' => [], 'shipmentId' => []]);

        $sectionProvider->getSection()->willReturn(new ShopApiSection());

        $requestStack->getCurrentRequest()->willReturn($request);

        $request->attributes = new ParameterBag(['tokenValue' => 'TOKEN', 'shipmentId' => '123']);

        $orderRepository->findCartByTokenValueAndChannel('TOKEN', $channel)->willReturn($cart);
        $cart->getId()->willReturn('321');
        $shipmentRepository->findOneByOrderId('123', '321')->willReturn($shipment);
        $cart->hasShipment($shipment)->willReturn(true);

        $normalizer
            ->normalize($shippingMethod, null, [
                'root_operation' => $operation,
                'sylius_api_channel' => $channel,
                'sylius_shipping_method_normalizer_already_called' => true,
            ])
            ->willReturn([])
        ;

        $shippingMethod->getCalculator()->willReturn('default_calculator');
        $shippingMethod->getConfiguration()->willReturn([]);

        $shippingCalculators->get('default_calculator')->willReturn($calculator);
        $calculator->calculate($shipment, [])->willReturn(1000);

        $this
            ->normalize($shippingMethod, null, [
                'root_operation' => $operation,
                'sylius_api_channel' => $channel,
            ])
            ->shouldReturn(['price' => 1000])
        ;
    }

    public function it_throws_an_exception_if_the_given_resource_is_not_an_instance_of_shipping_method_interface(
        SectionProviderInterface $sectionProvider,
        RequestStack $requestStack,
        NormalizerInterface $normalizer,
        ChannelInterface $channel,
    ): void {
        $operation = new GetCollection(uriVariables: ['tokenValue' => [], 'shipmentId' => []]);

        $sectionProvider->getSection()->shouldNotBeCalled();
        $requestStack->getCurrentRequest()->shouldNotBeCalled();
        $normalizer
            ->normalize(Argument::any(), null, [
                'root_operation' => $operation,
                'sylius_api_channel' => $channel,
                'sylius_shipping_method_normalizer_already_called' => true,
            ])
            ->shouldNotBeCalled()
        ;

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('normalize', [new \stdClass(), null, [
                'root_operation' => new GetCollection(uriVariables: ['tokenValue' => [], 'shipmentId' => []]),
                'sylius_api_channel' => $channel,
            ]])
        ;
    }

    public function it_throws_an_exception_if_serializer_has_already_been_called(
        SectionProviderInterface $sectionProvider,
        RequestStack $requestStack,
        NormalizerInterface $normalizer,
        ShippingMethodInterface $shippingMethod,
        ChannelInterface $channel,
    ): void {
        $operation = new GetCollection(uriVariables: ['tokenValue' => [], 'shipmentId' => []]);

        $sectionProvider->getSection()->shouldNotBeCalled();
        $requestStack->getCurrentRequest()->shouldNotBeCalled();
        $normalizer
            ->normalize($shippingMethod, null, [
                'root_operation' => $operation,
                'sylius_api_channel' => $channel,
                'sylius_shipping_method_normalizer_already_called' => true,
            ])
            ->shouldNotBeCalled()
        ;

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('normalize', [$shippingMethod, null, [
                'root_operation' => $operation,
                'sylius_api_channel' => $channel,
                'sylius_shipping_method_normalizer_already_called' => true,
            ]])
        ;
    }

    public function it_throws_an_exception_if_it_is_not_shop_section(
        SectionProviderInterface $sectionProvider,
        RequestStack $requestStack,
        NormalizerInterface $normalizer,
        ShippingMethodInterface $shippingMethod,
        ChannelInterface $channel,
    ): void {
        $operation = new GetCollection(uriVariables: ['tokenValue' => [], 'shipmentId' => []]);

        $sectionProvider->getSection()->willReturn(new AdminApiSection());

        $requestStack->getCurrentRequest()->shouldNotBeCalled();
        $normalizer
            ->normalize($shippingMethod, null, [
                'root_operation' => $operation,
                'sylius_api_channel' => $channel,
                'sylius_shipping_method_normalizer_already_called' => true,
            ])
            ->shouldNotBeCalled()
        ;

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('normalize', [$shippingMethod, null, [
                'root_operation' => $operation,
                'sylius_api_channel' => $channel,
            ]])
        ;
    }

    public function it_throws_an_exception_if_there_is_no_cart_for_given_token_value(
        SectionProviderInterface $sectionProvider,
        OrderRepositoryInterface $orderRepository,
        RequestStack $requestStack,
        Request $request,
        NormalizerInterface $normalizer,
        ShippingMethodInterface $shippingMethod,
        ChannelInterface $channel,
    ): void {
        $operation = new GetCollection(uriVariables: ['tokenValue' => [], 'shipmentId' => []]);

        $sectionProvider->getSection()->willReturn(new ShopApiSection());
        $requestStack->getCurrentRequest()->willReturn($request);
        $request->attributes = new ParameterBag(['tokenValue' => 'TOKEN', 'shipmentId' => '123']);

        $orderRepository->findCartByTokenValueAndChannel('TOKEN', $channel)->willReturn(null);

        $normalizer
            ->normalize($shippingMethod, null, [
                'root_operation' => $operation,
                'sylius_api_channel' => $channel,
                'sylius_shipping_method_normalizer_already_called' => true,
            ])
            ->shouldNotBeCalled()
        ;

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('normalize', [$shippingMethod, null, [
                'root_operation' => $operation,
                'sylius_api_channel' => $channel,
            ]])
        ;
    }

    public function it_throws_an_exception_if_there_is_no_shipment_for_given_id_and_cart(
        SectionProviderInterface $sectionProvider,
        OrderRepositoryInterface $orderRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        RequestStack $requestStack,
        Request $request,
        NormalizerInterface $normalizer,
        ShippingMethodInterface $shippingMethod,
        ChannelInterface $channel,
        OrderInterface $cart,
    ): void {
        $operation = new GetCollection(uriVariables: ['tokenValue' => [], 'shipmentId' => []]);

        $sectionProvider->getSection()->willReturn(new ShopApiSection());
        $requestStack->getCurrentRequest()->willReturn($request);
        $request->attributes = new ParameterBag(['tokenValue' => 'TOKEN', 'shipmentId' => '123']);

        $orderRepository->findCartByTokenValueAndChannel('TOKEN', $channel)->willReturn($cart);
        $cart->getId()->willReturn('321');
        $shipmentRepository->findOneByOrderId('123', '321')->willReturn(null);

        $normalizer
            ->normalize($shippingMethod, null, [
                'root_operation' => $operation,
                'sylius_api_channel' => $channel,
                'sylius_shipping_method_normalizer_already_called' => true,
            ])
            ->shouldNotBeCalled()
        ;

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('normalize', [$shippingMethod, null, [
                'root_operation' => $operation,
                'sylius_api_channel' => $channel,
            ]])
        ;
    }

    public function it_throws_an_exception_if_shipment_does_not_match_for_order(
        SectionProviderInterface $sectionProvider,
        OrderRepositoryInterface $orderRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        RequestStack $requestStack,
        Request $request,
        NormalizerInterface $normalizer,
        ShippingMethodInterface $shippingMethod,
        ChannelInterface $channel,
        OrderInterface $cart,
        ShipmentInterface $shipment,
    ): void {
        $operation = new GetCollection(uriVariables: ['tokenValue' => [], 'shipmentId' => []]);

        $sectionProvider->getSection()->willReturn(new ShopApiSection());
        $requestStack->getCurrentRequest()->willReturn($request);
        $request->attributes = new ParameterBag(['tokenValue' => 'TOKEN', 'shipmentId' => '123']);

        $orderRepository->findCartByTokenValueAndChannel('TOKEN', $channel)->willReturn($cart);
        $cart->getId()->willReturn('321');
        $shipmentRepository->findOneByOrderId('123', '321')->willReturn($shipment);
        $cart->hasShipment($shipment)->willReturn(false);

        $normalizer
            ->normalize($shippingMethod, null, [
                'root_operation' => $operation,
                'sylius_api_channel' => $channel,
                'sylius_shipping_method_normalizer_already_called' => true,
            ])
            ->willReturn([])
        ;

        $shippingMethod->getCalculator()->shouldNotBeCalled();
        $shippingMethod->getConfiguration()->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('normalize', [$shippingMethod, null, [
                'root_operation' => $operation,
                'sylius_api_channel' => $channel,
            ]])
        ;
    }
}
