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

namespace Tests\Sylius\Bundle\CoreBundle\PriceHistory\Remover;

use DateTimeImmutable;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\PriceHistory\Remover\ChannelPricingLogEntriesRemover;
use Sylius\Bundle\CoreBundle\PriceHistory\Remover\ChannelPricingLogEntriesRemoverInterface;
use Sylius\Component\Core\Model\ChannelPricingLogEntryInterface;
use Sylius\Component\Core\Repository\ChannelPricingLogEntryRepositoryInterface;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class ChannelPricingLogEntriesRemoverTest extends TestCase
{
    private ChannelPricingLogEntryRepositoryInterface&MockObject $repository;

    private MockObject&ObjectManager $manager;

    private ClockInterface&MockObject $clock;

    private EventDispatcherInterface&MockObject $eventDispatcher;

    private ChannelPricingLogEntriesRemover $remover;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(ChannelPricingLogEntryRepositoryInterface::class);
        $this->manager = $this->createMock(ObjectManager::class);
        $this->clock = $this->createMock(ClockInterface::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->remover = new ChannelPricingLogEntriesRemover(
            $this->repository,
            $this->manager,
            $this->clock,
            $this->eventDispatcher,
            1,
        );
    }

    public function testImplementsInterface(): void
    {
        $this->assertInstanceOf(ChannelPricingLogEntriesRemoverInterface::class, $this->remover);
    }

    public function testDoesNothingWhenNoEntriesFound(): void
    {
        $date = new DateTimeImmutable();
        $this->clock->method('now')->willReturn($date);

        $this->repository
            ->expects($this->once())
            ->method('findOlderThan')
            ->willReturn([])
        ;

        $this->manager->expects($this->never())->method('remove');
        $this->manager->expects($this->never())->method('flush');
        $this->manager->expects($this->never())->method('clear');

        $this->eventDispatcher->expects($this->never())->method('dispatch');

        $this->remover->remove(1);
    }

    public function testRemovesSingleBatch(): void
    {
        $date = new DateTimeImmutable();
        $logEntry = $this->createMock(ChannelPricingLogEntryInterface::class);

        $this->clock->method('now')->willReturn($date);

        $this->repository
            ->expects($this->exactly(2))
            ->method('findOlderThan')
            ->willReturnOnConsecutiveCalls([$logEntry], [])
        ;

        $this->manager->expects($this->once())->method('remove')->with($logEntry);
        $this->manager->expects($this->once())->method('flush');
        $this->manager->expects($this->once())->method('clear');

        $this->eventDispatcher->expects($this->exactly(2))->method('dispatch')->with($this->isInstanceOf(GenericEvent::class));

        $this->remover->remove(1);
    }

    public function testRemovesMultipleBatches(): void
    {
        $date = new DateTimeImmutable();
        $logEntry1 = $this->createMock(ChannelPricingLogEntryInterface::class);
        $logEntry2 = $this->createMock(ChannelPricingLogEntryInterface::class);

        $this->clock->method('now')->willReturn($date);

        $this->repository
            ->expects($this->exactly(3))
            ->method('findOlderThan')
            ->willReturnOnConsecutiveCalls([$logEntry1], [$logEntry2], [])
        ;

        $this->manager->expects($this->exactly(2))->method('remove')->with($this->logicalOr($logEntry1, $logEntry2));
        $this->manager->expects($this->exactly(2))->method('flush');
        $this->manager->expects($this->exactly(2))->method('clear');

        $this->eventDispatcher->expects($this->exactly(4))->method('dispatch')->with($this->isInstanceOf(GenericEvent::class));

        $this->remover->remove(1);
    }
}
