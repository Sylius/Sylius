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

namespace Tests\Sylius\Component\Channel\Context;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Channel\Context\SingleChannelContext;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;

final class SingleChannelContextTest extends TestCase
{
    /** @var MockObject&ChannelRepositoryInterface<ChannelInterface> */
    private ChannelRepositoryInterface $channelRepository;

    private SingleChannelContext $context;

    protected function setUp(): void
    {
        parent::setUp();
        $this->channelRepository = $this->createMock(ChannelRepositoryInterface::class);
        $this->context = new SingleChannelContext($this->channelRepository);
    }

    public function testShouldImplementChannelContextInterface(): void
    {
        self::assertInstanceOf(ChannelContextInterface::class, $this->context);
    }

    public function testShouldReturnsChannelIfItIsTheOnlyOneDefined(): void
    {
        $channel = $this->createMock(ChannelInterface::class);

        $this->channelRepository
            ->method('countAll')
            ->willReturn(1);
        $this->channelRepository
            ->method('findOneBy')
            ->with([])
            ->willReturn($channel);

        self::assertSame($channel, $this->context->getChannel());
    }

    public function testShouldThrowsExceptionIfThereAreNoChannelsDefined(): void
    {
        $this->channelRepository
            ->method('countAll')
            ->willReturn(0);

        self::expectException(ChannelNotFoundException::class);

        $this->context->getChannel();
    }

    public function testShouldThrowsExceptionIfThereAreManyChannelsDefined(): void
    {
        $this->channelRepository
            ->method('countAll')
            ->willReturn(2);

        self::expectException(ChannelNotFoundException::class);

        $this->context->getChannel();
    }
}
