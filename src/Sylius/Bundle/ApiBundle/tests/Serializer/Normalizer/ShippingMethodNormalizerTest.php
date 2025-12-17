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

    private MockObject&ShippingMethodInterface $shippingMethodMock;

    private MockObject&Request $requestMock;

    private ChannelInterface&MockObject $channelMock;

    private MockObject&OrderInterface $cartMock;

    private MockObject&ShipmentInterface $shipmentMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sectionProvider = $this->createMock(SectionProviderInterface::class);
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->shipmentRepository = $this->createMock(ShipmentRepositoryInterface::class);
        $this->shippingCalculators = $this->createMock(ServiceRegistryInterface::class);
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->normalizer = $this->createMock(NormalizerInterface::class);
        $this->shippingMethodNormalizer = new ShippingMethodNormalizer(
            $this->sectionProvider,
            $this->orderRepository,
            $this->shipmentRepository,
            $this->shippingCalculators,
            $this->requestStack,
            ['sylius:shipping_method:index'],
        );
        $this->shippingMethodNormalizer->setNormalizer($this->normalizer);
        $this->shippingMethodMock = $this->createMock(ShippingMethodInterface::class);
        $this->requestMock = $this->createMock(Request::class);
        $this->channelMock = $this->createMock(ChannelInterface::class);
        $this->cartMock = $this->createMock(OrderInterface::class);
        $this->shipmentMock = $this->createMock(ShipmentInterface::class);
    }

    public function testSupportsOnlyShippingMethodInterfaceInShopSectionWithProperData(): void
    {
        $this->sectionProvider = $this->createMock(SectionProviderInterface::class);
        $this->sectionProvider->method('getSection')->willReturn(new ShopApiSection());
        $this->shippingMethodNormalizer = new ShippingMethodNormalizer(
            $this->sectionProvider,
            $this->orderRepository,
            $this->shipmentRepository,
            $this->shippingCalculators,
            $this->requestStack,
            ['sylius:shipping_method:index'],
        );
        $this->shippingMethodNormalizer->setNormalizer($this->normalizer);
        self::assertTrue($this->shippingMethodNormalizer->supportsNormalization(
            $this->shippingMethodMock,
            null,
            [
                'root_operation' => new GetCollection(uriVariables: ['tokenValue' => [], 'shipmentId' => []]),
                'groups' => ['sylius:shipping_method:index'],
            ],
        ));
        $this->sectionProvider = $this->createMock(SectionProviderInterface::class);
        $this->sectionProvider->method('getSection')->willReturn(new ShopApiSection());
        $this->shippingMethodNormalizer = new ShippingMethodNormalizer(
            $this->sectionProvider,
            $this->orderRepository,
            $this->shipmentRepository,
            $this->shippingCalculators,
            $this->requestStack,
            ['sylius:shipping_method:index'],
        );
        $this->shippingMethodNormalizer->setNormalizer($this->normalizer);
        self::assertFalse($this->shippingMethodNormalizer->supportsNormalization(
            new \stdClass(),
            null,
            [
                'root_operation' => new GetCollection(uriVariables: ['tokenValue' => [], 'shipmentId' => []]),
                'groups' => ['sylius:shipping_method:index'],
            ],
        ));
        $this->sectionProvider = $this->createMock(SectionProviderInterface::class);
        $this->sectionProvider->method('getSection')->willReturn(new AdminApiSection());
        $this->shippingMethodNormalizer = new ShippingMethodNormalizer(
            $this->sectionProvider,
            $this->orderRepository,
            $this->shipmentRepository,
            $this->shippingCalculators,
            $this->requestStack,
            ['sylius:shipping_method:index'],
        );
        $this->shippingMethodNormalizer->setNormalizer($this->normalizer);
        self::assertFalse($this->shippingMethodNormalizer->supportsNormalization(
            $this->shippingMethodMock,
            null,
            [
                'root_operation' => new GetCollection(uriVariables: ['tokenValue' => [], 'shipmentId' => []]),
                'groups' => ['sylius:shipping_method:index'],
            ],
        ));
        $this->sectionProvider = $this->createMock(SectionProviderInterface::class);
        $this->sectionProvider->method('getSection')->willReturn(new ShopApiSection());
        $this->shippingMethodNormalizer = new ShippingMethodNormalizer(
            $this->sectionProvider,
            $this->orderRepository,
            $this->shipmentRepository,
            $this->shippingCalculators,
            $this->requestStack,
            ['sylius:shipping_method:index'],
        );
        $this->shippingMethodNormalizer->setNormalizer($this->normalizer);
        self::assertFalse($this->shippingMethodNormalizer->supportsNormalization(
            $this->shippingMethodMock,
            null,
            [
                'root_operation' => new GetCollection(uriVariables: ['tokenValue' => [], 'shipmentId' => []]),
                'groups' => ['sylius:shipping_method:show'],
            ],
        ));
        $this->sectionProvider = $this->createMock(SectionProviderInterface::class);
        $this->sectionProvider->method('getSection')->willReturn(new ShopApiSection());
        $this->shippingMethodNormalizer = new ShippingMethodNormalizer(
            $this->sectionProvider,
            $this->orderRepository,
            $this->shipmentRepository,
            $this->shippingCalculators,
            $this->requestStack,
            ['sylius:shipping_method:index'],
        );
        $this->shippingMethodNormalizer->setNormalizer($this->normalizer);
        self::assertFalse($this->shippingMethodNormalizer->supportsNormalization($this->shippingMethodMock));
        $this->sectionProvider = $this->createMock(SectionProviderInterface::class);
        $this->sectionProvider->method('getSection')->willReturn(new ShopApiSection());
        $this->shippingMethodNormalizer = new ShippingMethodNormalizer(
            $this->sectionProvider,
            $this->orderRepository,
            $this->shipmentRepository,
            $this->shippingCalculators,
            $this->requestStack,
            ['sylius:shipping_method:index'],
        );
        $this->shippingMethodNormalizer->setNormalizer($this->normalizer);
        self::assertFalse($this->shippingMethodNormalizer->supportsNormalization(
            $this->shippingMethodMock,
            null,
            [
                'root_operation' => new GetCollection(uriVariables: ['tokenValue' => []]),
                'groups' => ['sylius:shipping_method:index'],
            ],
        ));
        $this->sectionProvider = $this->createMock(SectionProviderInterface::class);
        $this->sectionProvider->method('getSection')->willReturn(new ShopApiSection());
        $this->shippingMethodNormalizer = new ShippingMethodNormalizer(
            $this->sectionProvider,
            $this->orderRepository,
            $this->shipmentRepository,
            $this->shippingCalculators,
            $this->requestStack,
            ['sylius:shipping_method:index'],
        );
        $this->shippingMethodNormalizer->setNormalizer($this->normalizer);
        self::assertFalse($this->shippingMethodNormalizer->supportsNormalization(
            $this->shippingMethodMock,
            null,
            [
                'root_operation' => new GetCollection(uriVariables: ['shipmentId' => []]),
                'groups' => ['sylius:shipping_method:index'],
            ],
        ));
    }

    public function testDoesNotSupportIfTheNormalizerHasBeenAlreadyCalled(): void
    {
        $this->sectionProvider->method('getSection')->willReturn(new ShopApiSection());
        self::assertFalse($this->shippingMethodNormalizer
            ->supportsNormalization($this->shippingMethodMock, null, [
                'sylius_shipping_method_normalizer_already_called' => true,
                'root_operation' => new GetCollection(uriVariables: ['tokenValue' => [], 'shipmentId' => []]),
            ]))
        ;
    }

    public function testAddsCalculatedPriceOfShippingMethod(): void
    {
        /** @var CalculatorInterface|MockObject $calculatorMock */
        $calculatorMock = $this->createMock(CalculatorInterface::class);
        $operation = new GetCollection(uriVariables: ['tokenValue' => [], 'shipmentId' => []]);
        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());
        $this->requestStack->expects(self::once())->method('getCurrentRequest')->willReturn($this->requestMock);
        $this->requestMock->attributes = new ParameterBag(['tokenValue' => 'TOKEN', 'shipmentId' => '123']);
        $this->orderRepository->expects(self::once())
            ->method('findCartByTokenValueAndChannel')
            ->with('TOKEN', $this->channelMock)
            ->willReturn($this->cartMock);
        $this->cartMock->expects(self::once())->method('getId')->willReturn('321');
        $this->shipmentRepository->expects(self::once())
            ->method('findOneByOrderId')
            ->with('123', '321')
            ->willReturn($this->shipmentMock);
        $this->cartMock->expects(self::once())
            ->method('hasShipment')
            ->with($this->shipmentMock)
            ->willReturn(true);
        $this->normalizer->expects(self::once())
            ->method('normalize')
            ->with($this->shippingMethodMock, null, [
            'root_operation' => $operation,
            'sylius_api_channel' => $this->channelMock,
            'sylius_shipping_method_normalizer_already_called' => true,
            'groups' => ['sylius:shipping_method:index'],
        ])
            ->willReturn([])
        ;
        $this->shippingMethodMock->expects(self::once())
            ->method('getCalculator')
            ->willReturn('default_calculator');
        $this->shippingMethodMock
            ->expects(self::once())
            ->method('getConfiguration')
            ->willReturn([]);
        $this->shippingCalculators->expects(self::once())
            ->method('get')
            ->with('default_calculator')
            ->willReturn($calculatorMock);
        $calculatorMock->expects(self::once())
            ->method('calculate')
            ->with($this->shipmentMock, [])
            ->willReturn(1000);
        self::assertSame(['price' => 1000], $this->shippingMethodNormalizer
            ->normalize($this->shippingMethodMock, null, [
                'root_operation' => $operation,
                'sylius_api_channel' => $this->channelMock,
                'groups' => ['sylius:shipping_method:index'],
            ]))
        ;
    }

    public function testThrowsAnExceptionIfTheGivenResourceIsNotAnInstanceOfShippingMethodInterface(): void
    {
        $this->sectionProvider->expects(self::never())->method('getSection');
        $this->requestStack->expects(self::never())->method('getCurrentRequest');
        $this->normalizer->expects(self::never())->method('normalize');
        $this->expectException(\InvalidArgumentException::class);
        $this->shippingMethodNormalizer->normalize(new \stdClass(), null, [
            'root_operation' => new GetCollection(uriVariables: ['tokenValue' => [], 'shipmentId' => []]),
            'sylius_api_channel' => $this->channelMock,
            'groups' => ['sylius:shipping_method:index'],
        ]);
    }

    public function testThrowsAnExceptionIfSerializerHasAlreadyBeenCalled(): void
    {
        $operation = new GetCollection(uriVariables: ['tokenValue' => [], 'shipmentId' => []]);
        $this->sectionProvider->expects(self::never())->method('getSection');
        $this->requestStack->expects(self::never())->method('getCurrentRequest');
        $this->normalizer->expects(self::never())
            ->method('normalize')
            ->with($this->shippingMethodMock, null, [
            'root_operation' => $operation,
            'sylius_api_channel' => $this->channelMock,
            'sylius_shipping_method_normalizer_already_called' => true,
            'groups' => ['sylius:shipping_method:index'],
        ]);
        $this->expectException(\InvalidArgumentException::class);
        $this->shippingMethodNormalizer->normalize($this->shippingMethodMock, null, [
            'root_operation' => $operation,
            'sylius_api_channel' => $this->channelMock,
            'sylius_shipping_method_normalizer_already_called' => true,
            'groups' => ['sylius:shipping_method:index'],
        ]);
    }

    public function testThrowsAnExceptionIfItIsNotShopSection(): void
    {
        $operation = new GetCollection(uriVariables: ['tokenValue' => [], 'shipmentId' => []]);
        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new AdminApiSection());
        $this->requestStack->expects(self::never())->method('getCurrentRequest');
        $this->normalizer->expects(self::never())
            ->method('normalize')
            ->with($this->shippingMethodMock, null, [
            'root_operation' => $operation,
            'sylius_api_channel' => $this->channelMock,
            'sylius_shipping_method_normalizer_already_called' => true,
            'groups' => ['sylius:shipping_method:index'],
        ]);
        $this->expectException(\InvalidArgumentException::class);
        $this->shippingMethodNormalizer->normalize($this->shippingMethodMock, null, [
            'root_operation' => $operation,
            'sylius_api_channel' => $this->channelMock,
            'groups' => ['sylius:shipping_method:index'],
        ]);
    }

    public function testThrowsAnExceptionIfSerializationGroupIsNotSupported(): void
    {
        $operation = new GetCollection(uriVariables: ['tokenValue' => [], 'shipmentId' => []]);
        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());
        $this->requestStack->expects(self::never())->method('getCurrentRequest');
        $this->normalizer->expects(self::never())
            ->method('normalize')
            ->with($this->shippingMethodMock, null, [
            'root_operation' => $operation,
            'sylius_api_channel' => $this->channelMock,
            'sylius_shipping_method_normalizer_already_called' => true,
            'groups' => ['sylius:shipping_method:shop'],
        ]);
        $this->expectException(\InvalidArgumentException::class);
        $this->shippingMethodNormalizer->normalize($this->shippingMethodMock, null, [
            'root_operation' => $operation,
            'sylius_api_channel' => $this->channelMock,
            'groups' => ['sylius:shipping_method:shop'],
        ]);
    }

    public function testThrowsAnExceptionIfThereIsNoCartForGivenTokenValue(): void
    {
        $operation = new GetCollection(uriVariables: ['tokenValue' => [], 'shipmentId' => []]);
        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());
        $this->requestStack->expects(self::once())->method('getCurrentRequest')->willReturn($this->requestMock);
        $this->requestMock->attributes = new ParameterBag(['tokenValue' => 'TOKEN', 'shipmentId' => '123']);
        $this->orderRepository->expects(self::once())
            ->method('findCartByTokenValueAndChannel')
            ->with('TOKEN', $this->channelMock)
            ->willReturn(null);
        $this->normalizer->expects(self::never())
            ->method('normalize')
            ->with($this->shippingMethodMock, null, [
            'root_operation' => $operation,
            'sylius_api_channel' => $this->channelMock,
            'sylius_shipping_method_normalizer_already_called' => true,
            'groups' => ['sylius:shipping_method:index'],
        ]);
        $this->expectException(\InvalidArgumentException::class);
        $this->shippingMethodNormalizer->normalize($this->shippingMethodMock, null, [
            'root_operation' => $operation,
            'sylius_api_channel' => $this->channelMock,
            'groups' => ['sylius:shipping_method:index'],
        ]);
    }

    public function testThrowsAnExceptionIfThereIsNoShipmentForGivenIdAndCart(): void
    {
        $operation = new GetCollection(uriVariables: ['tokenValue' => [], 'shipmentId' => []]);
        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());
        $this->requestStack->expects(self::once())->method('getCurrentRequest')->willReturn($this->requestMock);
        $this->requestMock->attributes = new ParameterBag(['tokenValue' => 'TOKEN', 'shipmentId' => '123']);
        $this->orderRepository->expects(self::once())
            ->method('findCartByTokenValueAndChannel')
            ->with('TOKEN', $this->channelMock)
            ->willReturn($this->cartMock);
        $this->cartMock->expects(self::once())->method('getId')->willReturn('321');
        $this->shipmentRepository->expects(self::once())
            ->method('findOneByOrderId')
            ->with('123', '321')
            ->willReturn(null);
        $this->normalizer->expects(self::never())
            ->method('normalize')
            ->with($this->shippingMethodMock, null, [
            'root_operation' => $operation,
            'sylius_api_channel' => $this->channelMock,
            'sylius_shipping_method_normalizer_already_called' => true,
            'groups' => ['sylius:shipping_method:index'],
        ]);
        $this->expectException(\InvalidArgumentException::class);
        $this->shippingMethodNormalizer->normalize($this->shippingMethodMock, null, [
            'root_operation' => $operation,
            'sylius_api_channel' => $this->channelMock,
            'groups' => ['sylius:shipping_method:index'],
        ]);
    }

    public function testThrowsAnExceptionIfShipmentDoesNotMatchForOrder(): void
    {
        $operation = new GetCollection(uriVariables: ['tokenValue' => [], 'shipmentId' => []]);
        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());
        $this->requestStack->expects(self::once())->method('getCurrentRequest')->willReturn($this->requestMock);
        $this->requestMock->attributes = new ParameterBag(['tokenValue' => 'TOKEN', 'shipmentId' => '123']);
        $this->orderRepository->expects(self::once())
            ->method('findCartByTokenValueAndChannel')
            ->with('TOKEN', $this->channelMock)
            ->willReturn($this->cartMock);
        $this->cartMock->expects(self::once())->method('getId')->willReturn('321');
        $this->shipmentRepository->expects(self::once())
            ->method('findOneByOrderId')
            ->with('123', '321')
            ->willReturn($this->shipmentMock);
        $this->cartMock->expects(self::once())
            ->method('hasShipment')
            ->with($this->shipmentMock)
            ->willReturn(false);
        $this->normalizer->expects(self::never())->method('normalize');
        $this->shippingMethodMock->expects(self::never())->method('getCalculator');
        $this->shippingMethodMock->expects(self::never())->method('getConfiguration');
        $this->expectException(\InvalidArgumentException::class);
        $this->shippingMethodNormalizer->normalize($this->shippingMethodMock, null, [
            'root_operation' => $operation,
            'sylius_api_channel' => $this->channelMock,
            'groups' => ['sylius:shipping_method:index'],
        ]);
    }
}
