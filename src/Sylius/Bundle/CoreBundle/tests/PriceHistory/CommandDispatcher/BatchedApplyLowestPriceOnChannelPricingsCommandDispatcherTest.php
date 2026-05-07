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

namespace Tests\Sylius\Bundle\CoreBundle\PriceHistory\CommandDispatcher;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\PriceHistory\Command\ApplyLowestPriceOnChannelPricings;
use Sylius\Bundle\CoreBundle\PriceHistory\CommandDispatcher\ApplyLowestPriceOnChannelPricingsCommandDispatcherInterface;
use Sylius\Bundle\CoreBundle\PriceHistory\CommandDispatcher\BatchedApplyLowestPriceOnChannelPricingsCommandDispatcher;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class BatchedApplyLowestPriceOnChannelPricingsCommandDispatcherTest extends TestCase
{
    private MockObject&RepositoryInterface $channelPricingRepository;

    private MessageBusInterface&MockObject $commandBus;

    private BatchedApplyLowestPriceOnChannelPricingsCommandDispatcher $dispatcher;

    protected function setUp(): void
    {
        $this->channelPricingRepository = $this->createMock(RepositoryInterface::class);
        $this->commandBus = $this->createMock(MessageBusInterface::class);

        $this->dispatcher = new BatchedApplyLowestPriceOnChannelPricingsCommandDispatcher(
            $this->channelPricingRepository,
            $this->commandBus,
            2,
        );
    }

    public function testImplementsInterface(): void
    {
        $this->assertInstanceOf(
            ApplyLowestPriceOnChannelPricingsCommandDispatcherInterface::class,
            $this->dispatcher,
        );
    }

    public function testDispatchesApplicationsOfLowestPriceOnChannelPricingWithinChannelInBatches(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $channel->method('getCode')->willReturn('WEB');

        $cp1 = $this->createMock(ChannelPricingInterface::class);
        $cp2 = $this->createMock(ChannelPricingInterface::class);
        $cp3 = $this->createMock(ChannelPricingInterface::class);
        $cp4 = $this->createMock(ChannelPricingInterface::class);
        $cp5 = $this->createMock(ChannelPricingInterface::class);

        $cp1->method('getId')->willReturn(1);
        $cp2->method('getId')->willReturn(2);
        $cp3->method('getId')->willReturn(6);
        $cp4->method('getId')->willReturn(7);
        $cp5->method('getId')->willReturn(9);

        $this->channelPricingRepository
            ->method('findBy')
            ->willReturnCallback(fn ($criteria, $orderBy, $limit, $offset) => match ($offset) {
                0 => [$cp1, $cp2],
                2 => [$cp3, $cp4],
                4 => [$cp5],
                default => [],
            })
        ;

        $dispatchedCommands = [];
        $this->commandBus
            ->method('dispatch')
            ->willReturnCallback(function ($command) use (&$dispatchedCommands) {
                $this->assertInstanceOf(ApplyLowestPriceOnChannelPricings::class, $command);
                $dispatchedCommands[] = $command;

                return new Envelope($command);
            })
        ;

        $this->dispatcher->applyWithinChannel($channel);

        $this->assertCount(3, $dispatchedCommands, 'Expected 3 dispatches of ApplyLowestPriceOnChannelPricings');
    }
}
