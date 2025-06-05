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

namespace Tests\Sylius\Bundle\ApiBundle\StateProvider\Shop\Order\OrderItem\Adjustment;

use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Doctrine\Common\Collections\ArrayCollection;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\ApiBundle\StateProvider\Shop\Order\OrderItem\Adjustment\CollectionProvider;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderItem;
use Sylius\Component\Core\Repository\OrderItemRepositoryInterface;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;

final class CollectionProviderTest extends TestCase
{
    private MockObject&OrderItemRepositoryInterface $orderItemRepository;

    private MockObject&SectionProviderInterface $sectionProvider;

    private CollectionProvider $collectionProvider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderItemRepository = $this->createMock(OrderItemRepositoryInterface::class);
        $this->sectionProvider = $this->createMock(SectionProviderInterface::class);
        $this->collectionProvider = new CollectionProvider($this->orderItemRepository, $this->sectionProvider);
    }

    public function testIsAStateProvider(): void
    {
        self::assertInstanceOf(ProviderInterface::class, $this->collectionProvider);
    }

    public function testReturnsEmptyArrayWhenUriVariablesHaveNoId(): void
    {
        $operation = new GetCollection(class: AdjustmentInterface::class);

        $this->sectionProvider
            ->expects(self::once())
            ->method('getSection')
            ->willReturn(new ShopApiSection());

        $this->orderItemRepository
            ->expects(self::never())
            ->method('findOneByIdAndOrderTokenValue');

        $result = $this->collectionProvider->provide($operation, ['tokenValue' => '42']);

        self::assertEquals([], $result);
    }

    public function testReturnsEmptyArrayWhenUriVariablesHaveNoTokenValue(): void
    {
        $operation = new GetCollection(class: AdjustmentInterface::class);

        $this->sectionProvider
            ->expects(self::once())
            ->method('getSection')
            ->willReturn(new ShopApiSection());

        $this->orderItemRepository
            ->expects(self::never())
            ->method('findOneByIdAndOrderTokenValue');

        $result = $this->collectionProvider->provide($operation, ['id' => 42]);

        self::assertEquals([], $result);
    }

    public function testReturnsEmptyArrayWhenNoOrderItemCanBeFound(): void
    {
        $operation = new GetCollection(class: AdjustmentInterface::class);

        $this->sectionProvider
            ->expects(self::once())
            ->method('getSection')
            ->willReturn(new ShopApiSection());

        $this->orderItemRepository
            ->expects(self::once())
            ->method('findOneByIdAndOrderTokenValue')
            ->with(42, 'token')
            ->willReturn(null);

        $result = $this->collectionProvider->provide($operation, ['id' => '42', 'tokenValue' => 'token']);

        self::assertEquals([], $result);
    }

    public function testReturnsAdjustmentsRecursively(): void
    {
        $operation = new GetCollection(class: AdjustmentInterface::class);

        $request = $this->createMock(Request::class);

        $orderItem = $this->createMock(OrderItem::class);

        $firstAdjustment = $this->createMock(AdjustmentInterface::class);

        $secondAdjustment = $this->createMock(AdjustmentInterface::class);

        $request->query = new InputBag(['type' => 'type']);

        $adjustments = new ArrayCollection([$firstAdjustment, $secondAdjustment]);

        $this->sectionProvider
            ->expects(self::once())
            ->method('getSection')
            ->willReturn(new ShopApiSection());

        $orderItem
            ->expects(self::once())
            ->method('getAdjustmentsRecursively')
            ->with('type')
            ->willReturn($adjustments);

        $this->orderItemRepository
            ->expects(self::once())
            ->method('findOneByIdAndOrderTokenValue')
            ->with(42, 'token')
            ->willReturn($orderItem);

        $result = $this->collectionProvider->provide(
            $operation,
            ['id' => '42', 'tokenValue' => 'token'],
            ['request' => $request],
        );

        self::assertEquals($adjustments, $result);
    }

    public function testThrowsAnExceptionWhenOperationClassIsNotAdjustment(): void
    {
        $operation = $this->createMock(Operation::class);

        $operation
            ->expects(self::once())
            ->method('getClass')
            ->willReturn(\stdClass::class);

        self::expectException(InvalidArgumentException::class);

        $this->collectionProvider->provide($operation);
    }

    public function testThrowsAnExceptionWhenOperationIsNotGetCollection(): void
    {
        $operationMock = $this->createMock(Operation::class);

        $operationMock
            ->expects(self::once())
            ->method('getClass')
            ->willReturn(AdjustmentInterface::class);

        $this->sectionProvider
            ->method('getSection')
            ->willReturn(new ShopApiSection());

        self::expectException(InvalidArgumentException::class);

        $this->collectionProvider->provide($operationMock);
    }

    public function testThrowsAnExceptionWhenOperationIsNotInShopApiSection(): void
    {
        $operation = new GetCollection(class: AdjustmentInterface::class);

        $this->sectionProvider
            ->expects(self::once())
            ->method('getSection')
            ->willReturn(new AdminApiSection());

        self::expectException(InvalidArgumentException::class);

        $this->collectionProvider->provide($operation);
    }
}
