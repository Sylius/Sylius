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

namespace Tests\Sylius\Bundle\ApiBundle\StateProvider\Shop\Order\Shipment\ShippingMethod;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\ApiBundle\StateProvider\Shop\Order\Shipment\ShippingMethod\CollectionProvider;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethod;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Sylius\Component\Shipping\Resolver\ShippingMethodsResolverInterface;

final class CollectionProviderTest extends TestCase
{
    private MockObject&SectionProviderInterface $sectionProvider;

    private MockObject&ShipmentRepositoryInterface $shipmentRepository;

    private MockObject&ShippingMethodsResolverInterface $shippingMethodsResolver;

    private CollectionProvider $collectionProvider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sectionProvider = $this->createMock(SectionProviderInterface::class);
        $this->shipmentRepository = $this->createMock(ShipmentRepositoryInterface::class);
        $this->shippingMethodsResolver = $this->createMock(ShippingMethodsResolverInterface::class);
        $this->collectionProvider = new CollectionProvider(
            $this->sectionProvider,
            $this->shipmentRepository,
            $this->shippingMethodsResolver,
        );
    }

    public function testProvidesShippingMethods(): void
    {
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);
        /** @var ShipmentInterface|MockObject $shipmentMock */
        $shipmentMock = $this->createMock(ShipmentInterface::class);
        /** @var ShippingMethodInterface|MockObject $methodMock */
        $methodMock = $this->createMock(ShippingMethodInterface::class);

        $operation = new GetCollection(class: ShippingMethod::class);

        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());

        $this->shipmentRepository->expects(self::once())
            ->method('findOneByOrderTokenAndChannel')
            ->with(1, 'TOKEN', $channelMock)
            ->willReturn($shipmentMock);

        $this->shippingMethodsResolver->expects(self::once())
            ->method('getSupportedMethods')
            ->with($shipmentMock)
            ->willReturn([$methodMock]);

        self::assertSame([$methodMock], $this->collectionProvider
            ->provide($operation, ['tokenValue' => 'TOKEN', 'shipmentId' => 1], ['sylius_api_channel' => $channelMock]))
        ;
    }

    public function testThrowsAnExceptionWhenResourceIsNotAShippingMethodInterface(): void
    {
        $operation = new GetCollection(class: \stdClass::class);

        self::expectException(\InvalidArgumentException::class);

        $this->collectionProvider->provide($operation);
    }

    public function testThrowsAnExceptionWhenOperationIsNotGetCollection(): void
    {
        $operation = new Get(class: ShippingMethod::class);

        self::expectException(\InvalidArgumentException::class);

        $this->collectionProvider->provide($operation);
    }

    public function testThrowsAnExceptionWhenOperationIsNotInShopApiSection(): void
    {
        $operation = new GetCollection(class: ShippingMethod::class);

        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new AdminApiSection());

        self::expectException(\InvalidArgumentException::class);

        $this->collectionProvider->provide($operation);
    }

    public function testThrowsAnExceptionWhenUriVariablesDoNotExist(): void
    {
        $operation = new GetCollection(class: ShippingMethod::class);

        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());

        self::expectException(\InvalidArgumentException::class);

        $this->collectionProvider->provide($operation);
    }
}
