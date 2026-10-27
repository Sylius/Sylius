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

namespace Tests\Sylius\Bundle\ApiBundle\StateProvider\Admin\Order;

use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\StateProvider\Admin\Order\CollectionProvider;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;

#[CoversClass(CollectionProvider::class)]
final class CollectionProviderTest extends TestCase
{
    private MockObject&ProviderInterface $decoratedProvider;

    /** @var MockObject&OrderRepositoryInterface<OrderInterface> */
    private MockObject&OrderRepositoryInterface $orderRepository;

    private CollectionProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->decoratedProvider = $this->createMock(ProviderInterface::class);
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->provider = new CollectionProvider($this->decoratedProvider, $this->orderRepository);
    }

    public function testPreloadsAdjustmentsForOrdersReturnedByTheDecoratedProvider(): void
    {
        $operation = new GetCollection();
        $firstOrder = $this->createMock(OrderInterface::class);
        $secondOrder = $this->createMock(OrderInterface::class);

        $this->decoratedProvider
            ->expects($this->once())
            ->method('provide')
            ->with($operation, ['page' => 1], ['filters' => []])
            ->willReturn(new \ArrayIterator([$firstOrder, $secondOrder]))
        ;

        $this->orderRepository
            ->expects($this->once())
            ->method('preloadAdjustments')
            ->with([$firstOrder, $secondOrder])
        ;

        $result = $this->provider->provide($operation, ['page' => 1], ['filters' => []]);

        $this->assertEquals([$firstOrder, $secondOrder], iterator_to_array($result));
    }

    public function testPreloadsNothingWhenTheCollectionIsEmpty(): void
    {
        $operation = $this->createMock(Operation::class);

        $this->decoratedProvider
            ->expects($this->once())
            ->method('provide')
            ->willReturn([])
        ;

        $this->orderRepository
            ->expects($this->once())
            ->method('preloadAdjustments')
            ->with([])
        ;

        $this->assertSame([], $this->provider->provide($operation));
    }
}
