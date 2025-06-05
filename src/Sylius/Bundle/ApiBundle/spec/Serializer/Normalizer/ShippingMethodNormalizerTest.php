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

namespace Tests\Sylius\Bundle\ApiBundle\Serializer\Normalizer;

use ApiPlatform\Metadata\GetCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\ApiBundle\Serializer\Normalizer\ShippingMethodNormalizer;
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

final class ShippingMethodNormalizerTest extends TestCase
{
    private MockObject&SectionProviderInterface $sectionProvider;

    private MockObject&OrderRepositoryInterface $orderRepository;

    private MockObject&ShipmentRepositoryInterface $shipmentRepository;

    private MockObject&ServiceRegistryInterface $shippingCalculators;

    private MockObject&RequestStack $requestStack;

    private MockObject&NormalizerInterface $normalizer;

    private ShippingMethodNormalizer $shippingMethodNormalizer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sectionProvider = $this->createMock(SectionProviderInterface::class);
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->shipmentRepository = $this->createMock(ShipmentRepositoryInterface::class);
        $this->shippingCalculators = $this->createMock(ServiceRegistryInterface::class);
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->normalizer = $this->createMock(NormalizerInterface::class);
        $this->shippingMethodNormalizer = new ShippingMethodNormalizer($this->sectionProvider, $this->orderRepository, $this->shipmentRepository, $this->shippingCalculators, $this->requestStack, ['sylius:shipping_method:index']);
        $this->shippingMethodNormalizer->setNormalizer($this->normalizer);
    }

    public function testSupportsOnlyShippingMethodInterfaceInShopSectionWithProperData(): void
    {
        /** @var ShippingMethodInterface|MockObject $shippingMethodMock */
        $shippingMethodMock = $this->createMock(ShippingMethodInterface::class);
        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());
        self::assertTrue($this->shippingMethodNormalizer
            ->supportsNormalization($shippingMethodMock, null, [
                'root_operation' => new GetCollection(uriVariables: ['tokenValue' => [], 'shipmentId' => []]),
                'groups' => ['sylius:shipping_method:index'],
            ]))
        ;
        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());
        self::assertFalse($this->shippingMethodNormalizer
            ->supportsNormalization(new \stdClass(), null, [
                'root_operation' => new GetCollection(uriVariables: ['tokenValue' => [], 'shipmentId' => []]),
                'groups' => ['sylius:shipping_method:index'],
            ]))
        ;
        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new AdminApiSection());
        self::assertFalse($this->shippingMethodNormalizer
            ->supportsNormalization($shippingMethodMock, null, [
                'root_operation' => new GetCollection(uriVariables: ['tokenValue' => [], 'shipmentId' => []]),
                'groups' => ['sylius:shipping_method:index'],
            ]))
        ;
        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());
        self::assertFalse($this->shippingMethodNormalizer
            ->supportsNormalization($shippingMethodMock, null, [
                'root_operation' => new GetCollection(uriVariables: ['tokenValue' => [], 'shipmentId' => []]),
                'groups' => ['sylius:shipping_method:show'],
            ]))
        ;
        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());
        self::assertFalse($this->shippingMethodNormalizer->supportsNormalization($shippingMethodMock));
        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());
        self::assertFalse($this->shippingMethodNormalizer
            ->supportsNormalization($shippingMethodMock, null, [
                'root_operation' => new GetCollection(uriVariables: ['tokenValue' => []]),
                'groups' => ['sylius:shipping_method:index'],
            ]))
        ;
        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());
        self::assertFalse($this->shippingMethodNormalizer
            ->supportsNormalization($shippingMethodMock, null, [
                'root_operation' => new GetCollection(uriVariables: ['shipmentId' => []]),
                'groups' => ['sylius:shipping_method:index'],
            ]))
        ;
    }

    public function testDoesNotSupportIfTheNormalizerHasBeenAlreadyCalled(): void
    {
        /** @var ShippingMethodInterface|MockObject $shippingMethodMock */
        $shippingMethodMock = $this->createMock(ShippingMethodInterface::class);
        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());
        self::assertFalse($this->shippingMethodNormalizer
            ->supportsNormalization($shippingMethodMock, null, [
                'sylius_shipping_method_normalizer_already_called' => true,
                'root_operation' => new GetCollection(uriVariables: ['tokenValue' => [], 'shipmentId' => []]),
            ]))
        ;
    }

    public function testAddsCalculatedPriceOfShippingMethod(): void
    {
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);
        /** @var ShippingMethodInterface|MockObject $shippingMethodMock */
        $shippingMethodMock = $this->createMock(ShippingMethodInterface::class);
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);
        /** @var OrderInterface|MockObject $cartMock */
        $cartMock = $this->createMock(OrderInterface::class);
        /** @var ShipmentInterface|MockObject $shipmentMock */
        $shipmentMock = $this->createMock(ShipmentInterface::class);
        /** @var CalculatorInterface|MockObject $calculatorMock */
        $calculatorMock = $this->createMock(CalculatorInterface::class);
        $operation = new GetCollection(uriVariables: ['tokenValue' => [], 'shipmentId' => []]);
        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());
        $this->requestStack->expects(self::once())->method('getCurrentRequest')->willReturn($requestMock);
        $requestMock->attributes = new ParameterBag(['tokenValue' => 'TOKEN', 'shipmentId' => '123']);
        $this->orderRepository->expects(self::once())->method('findCartByTokenValueAndChannel')->with('TOKEN', $channelMock)->willReturn($cartMock);
        $cartMock->expects(self::once())->method('getId')->willReturn('321');
        $this->shipmentRepository->expects(self::once())->method('findOneByOrderId')->with('123', '321')->willReturn($shipmentMock);
        $cartMock->expects(self::once())->method('hasShipment')->with($shipmentMock)->willReturn(true);
        $this->normalizer->expects(self::once())->method('normalize')->with($shippingMethodMock, null, [
            'root_operation' => $operation,
            'sylius_api_channel' => $channelMock,
            'sylius_shipping_method_normalizer_already_called' => true,
            'groups' => ['sylius:shipping_method:index'],
        ])
            ->willReturn([])
        ;
        $shippingMethodMock->expects(self::once())->method('getCalculator')->willReturn('default_calculator');
        $shippingMethodMock->expects(self::once())->method('getConfiguration')->willReturn([]);
        $this->shippingCalculators->expects(self::once())->method('get')->with('default_calculator')->willReturn($calculatorMock);
        $calculatorMock->expects(self::once())->method('calculate')->with($shipmentMock, [])->willReturn(1000);
        self::assertSame(['price' => 1000], $this->shippingMethodNormalizer
            ->normalize($shippingMethodMock, null, [
                'root_operation' => $operation,
                'sylius_api_channel' => $channelMock,
                'groups' => ['sylius:shipping_method:index'],
            ]))
        ;
    }

    public function testThrowsAnExceptionIfTheGivenResourceIsNotAnInstanceOfShippingMethodInterface(): void
    {
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);
        $operation = new GetCollection(uriVariables: ['tokenValue' => [], 'shipmentId' => []]);
        $this->sectionProvider->expects(self::never())->method('getSection');
        $this->requestStack->expects(self::never())->method('getCurrentRequest');
        $this->normalizer->expects(self::never())->method('normalize')
        ;
        $this->expectException(\InvalidArgumentException::class);
        $this->shippingMethodNormalizer->normalize(new \stdClass(), null, [
            'root_operation' => new GetCollection(uriVariables: ['tokenValue' => [], 'shipmentId' => []]),
            'sylius_api_channel' => $channelMock,
            'groups' => ['sylius:shipping_method:index'],
        ]);
    }

    public function testThrowsAnExceptionIfSerializerHasAlreadyBeenCalled(): void
    {
        /** @var ShippingMethodInterface|MockObject $shippingMethodMock */
        $shippingMethodMock = $this->createMock(ShippingMethodInterface::class);
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);
        $operation = new GetCollection(uriVariables: ['tokenValue' => [], 'shipmentId' => []]);
        $this->sectionProvider->expects(self::never())->method('getSection');
        $this->requestStack->expects(self::never())->method('getCurrentRequest');
        $this->normalizer->expects(self::never())->method('normalize')->with($shippingMethodMock, null, [
            'root_operation' => $operation,
            'sylius_api_channel' => $channelMock,
            'sylius_shipping_method_normalizer_already_called' => true,
            'groups' => ['sylius:shipping_method:index'],
        ])
        ;
        $this->expectException(\InvalidArgumentException::class);
        $this->shippingMethodNormalizer->normalize($shippingMethodMock, null, [
            'root_operation' => $operation,
            'sylius_api_channel' => $channelMock,
            'sylius_shipping_method_normalizer_already_called' => true,
            'groups' => ['sylius:shipping_method:index'],
        ]);
    }

    public function testThrowsAnExceptionIfItIsNotShopSection(): void
    {
        /** @var ShippingMethodInterface|MockObject $shippingMethodMock */
        $shippingMethodMock = $this->createMock(ShippingMethodInterface::class);
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);
        $operation = new GetCollection(uriVariables: ['tokenValue' => [], 'shipmentId' => []]);
        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new AdminApiSection());
        $this->requestStack->expects(self::never())->method('getCurrentRequest');
        $this->normalizer->expects(self::never())->method('normalize')->with($shippingMethodMock, null, [
            'root_operation' => $operation,
            'sylius_api_channel' => $channelMock,
            'sylius_shipping_method_normalizer_already_called' => true,
            'groups' => ['sylius:shipping_method:index'],
        ])
        ;
        $this->expectException(\InvalidArgumentException::class);
        $this->shippingMethodNormalizer->normalize($shippingMethodMock, null, [
            'root_operation' => $operation,
            'sylius_api_channel' => $channelMock,
            'groups' => ['sylius:shipping_method:index'],
        ]);
    }

    public function testThrowsAnExceptionIfSerializationGroupIsNotSupported(): void
    {
        /** @var ShippingMethodInterface|MockObject $shippingMethodMock */
        $shippingMethodMock = $this->createMock(ShippingMethodInterface::class);
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);
        $operation = new GetCollection(uriVariables: ['tokenValue' => [], 'shipmentId' => []]);
        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());
        $this->requestStack->expects(self::never())->method('getCurrentRequest');
        $this->normalizer->expects(self::never())->method('normalize')->with($shippingMethodMock, null, [
            'root_operation' => $operation,
            'sylius_api_channel' => $channelMock,
            'sylius_shipping_method_normalizer_already_called' => true,
            'groups' => ['sylius:shipping_method:shop'],
        ])
        ;
        $this->expectException(\InvalidArgumentException::class);
        $this->shippingMethodNormalizer->normalize($shippingMethodMock, null, [
            'root_operation' => $operation,
            'sylius_api_channel' => $channelMock,
            'groups' => ['sylius:shipping_method:shop'],
        ]);
    }

    public function testThrowsAnExceptionIfThereIsNoCartForGivenTokenValue(): void
    {
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);
        /** @var ShippingMethodInterface|MockObject $shippingMethodMock */
        $shippingMethodMock = $this->createMock(ShippingMethodInterface::class);
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);
        $operation = new GetCollection(uriVariables: ['tokenValue' => [], 'shipmentId' => []]);
        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());
        $this->requestStack->expects(self::once())->method('getCurrentRequest')->willReturn($requestMock);
        $requestMock->attributes = new ParameterBag(['tokenValue' => 'TOKEN', 'shipmentId' => '123']);
        $this->orderRepository->expects(self::once())->method('findCartByTokenValueAndChannel')->with('TOKEN', $channelMock)->willReturn(null);
        $this->normalizer->expects(self::never())->method('normalize')->with($shippingMethodMock, null, [
            'root_operation' => $operation,
            'sylius_api_channel' => $channelMock,
            'sylius_shipping_method_normalizer_already_called' => true,
            'groups' => ['sylius:shipping_method:index'],
        ])
        ;
        $this->expectException(\InvalidArgumentException::class);
        $this->shippingMethodNormalizer->normalize($shippingMethodMock, null, [
            'root_operation' => $operation,
            'sylius_api_channel' => $channelMock,
            'groups' => ['sylius:shipping_method:index'],
        ]);
    }

    public function testThrowsAnExceptionIfThereIsNoShipmentForGivenIdAndCart(): void
    {
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);
        /** @var ShippingMethodInterface|MockObject $shippingMethodMock */
        $shippingMethodMock = $this->createMock(ShippingMethodInterface::class);
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);
        /** @var OrderInterface|MockObject $cartMock */
        $cartMock = $this->createMock(OrderInterface::class);
        $operation = new GetCollection(uriVariables: ['tokenValue' => [], 'shipmentId' => []]);
        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());
        $this->requestStack->expects(self::once())->method('getCurrentRequest')->willReturn($requestMock);
        $requestMock->attributes = new ParameterBag(['tokenValue' => 'TOKEN', 'shipmentId' => '123']);
        $this->orderRepository->expects(self::once())->method('findCartByTokenValueAndChannel')->with('TOKEN', $channelMock)->willReturn($cartMock);
        $cartMock->expects(self::once())->method('getId')->willReturn('321');
        $this->shipmentRepository->expects(self::once())->method('findOneByOrderId')->with('123', '321')->willReturn(null);
        $this->normalizer->expects(self::never())->method('normalize')->with($shippingMethodMock, null, [
            'root_operation' => $operation,
            'sylius_api_channel' => $channelMock,
            'sylius_shipping_method_normalizer_already_called' => true,
            'groups' => ['sylius:shipping_method:index'],
        ])
        ;
        $this->expectException(\InvalidArgumentException::class);
        $this->shippingMethodNormalizer->normalize($shippingMethodMock, null, [
            'root_operation' => $operation,
            'sylius_api_channel' => $channelMock,
            'groups' => ['sylius:shipping_method:index'],
        ]);
    }

    public function testThrowsAnExceptionIfShipmentDoesNotMatchForOrder(): void
    {
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);
        /** @var ShippingMethodInterface|MockObject $shippingMethodMock */
        $shippingMethodMock = $this->createMock(ShippingMethodInterface::class);
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);
        /** @var OrderInterface|MockObject $cartMock */
        $cartMock = $this->createMock(OrderInterface::class);
        /** @var ShipmentInterface|MockObject $shipmentMock */
        $shipmentMock = $this->createMock(ShipmentInterface::class);
        $operation = new GetCollection(uriVariables: ['tokenValue' => [], 'shipmentId' => []]);
        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());
        $this->requestStack->expects(self::once())->method('getCurrentRequest')->willReturn($requestMock);
        $requestMock->attributes = new ParameterBag(['tokenValue' => 'TOKEN', 'shipmentId' => '123']);
        $this->orderRepository->expects(self::once())->method('findCartByTokenValueAndChannel')->with('TOKEN', $channelMock)->willReturn($cartMock);
        $cartMock->expects(self::once())->method('getId')->willReturn('321');
        $this->shipmentRepository->expects(self::once())->method('findOneByOrderId')->with('123', '321')->willReturn($shipmentMock);
        $cartMock->expects(self::once())->method('hasShipment')->with($shipmentMock)->willReturn(false);
        $this->normalizer->expects(self::once())->method('normalize')->with($shippingMethodMock, null, [
            'root_operation' => $operation,
            'sylius_api_channel' => $channelMock,
            'sylius_shipping_method_normalizer_already_called' => true,
            'groups' => ['sylius:shipping_method:index'],
        ])
            ->willReturn([])
        ;
        $shippingMethodMock->expects(self::never())->method('getCalculator');
        $shippingMethodMock->expects(self::never())->method('getConfiguration');
        $this->expectException(\InvalidArgumentException::class);
        $this->shippingMethodNormalizer->normalize($shippingMethodMock, null, [
            'root_operation' => $operation,
            'sylius_api_channel' => $channelMock,
            'groups' => ['sylius:shipping_method:index'],
        ]);
    }
}
