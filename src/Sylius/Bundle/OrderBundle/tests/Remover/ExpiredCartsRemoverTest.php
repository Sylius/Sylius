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

namespace Tests\Sylius\Bundle\OrderBundle\Remover;

use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\OrderBundle\Remover\ExpiredCartsRemover;
use Sylius\Bundle\OrderBundle\SyliusExpiredCartsEvents;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Remover\ExpiredCartsRemoverInterface;
use Sylius\Component\Order\Repository\OrderRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

final class ExpiredCartsRemoverTest extends TestCase
{
    private MockObject&OrderRepositoryInterface $orderRepository;

    private MockObject&ObjectManager $objectManager;

    private EventDispatcher&MockObject $eventDispatcher;

    private ExpiredCartsRemover $expiredCartsRemover;

    private MockObject&OrderInterface $cart;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->objectManager = $this->createMock(ObjectManager::class);
        $this->eventDispatcher = $this->createMock(EventDispatcher::class);
        $this->expiredCartsRemover = new ExpiredCartsRemover(
            $this->orderRepository,
            $this->objectManager,
            $this->eventDispatcher,
            '2 months',
        );
        $this->cart = $this->createMock(OrderInterface::class);
    }

    public function testImplementsAnExpiredCartsRemoverInterface(): void
    {
        self::assertInstanceOf(ExpiredCartsRemoverInterface::class, $this->expiredCartsRemover);
    }

    public function testRemovesACartWhichHasBeenUpdatedBeforeConfiguredDate(): void
    {
        $secondCart = $this->createMock(OrderInterface::class);

        $this->orderRepository
            ->expects(self::exactly(2))
            ->method('findCartsNotModifiedSince')
            ->with($this->isInstanceOf('DateTimeInterface'), 100)
            ->willReturnOnConsecutiveCalls(
                [$this->cart, $secondCart],
                [],
            );

        $removedCarts = [];

        $this->objectManager->expects(self::exactly(2))
            ->method('remove')
            ->with($this->callback(function ($cart) use ($secondCart, &$removedCarts) {
                $removedCarts[] = $cart;

                return in_array($cart, [$this->cart, $secondCart], true);
            }));

        $this->objectManager->expects(self::once())->method('flush');
        $this->objectManager->expects(self::once())->method('clear');

        $this->expiredCartsRemover->remove();

        self::assertSame([$this->cart, $secondCart], $removedCarts);
    }

    public function testRemovesCartsInBatches(): void
    {
        $this->orderRepository
            ->expects(self::exactly(3))
            ->method('findCartsNotModifiedSince')
            ->with($this->isInstanceOf('DateTimeInterface'), 100)
            ->willReturnOnConsecutiveCalls(
                array_fill(0, 100, $this->cart),
                array_fill(0, 100, $this->cart),
                [],
            );

        $this->eventDispatcher
            ->expects(self::exactly(4))
            ->method('dispatch')
            ->willReturnCallback(function ($event, $eventName) {
                self::assertContains(
                    $eventName,
                    [SyliusExpiredCartsEvents::PRE_REMOVE, SyliusExpiredCartsEvents::POST_REMOVE],
                );

                return $event;
            });

        $this->objectManager
            ->expects(self::exactly(200))
            ->method('remove')
            ->with($this->isInstanceOf(OrderInterface::class));

        $this->objectManager->expects(self::exactly(2))->method('flush');

        $this->objectManager->expects(self::exactly(2))->method('clear');

        $this->expiredCartsRemover->remove();
    }
}
