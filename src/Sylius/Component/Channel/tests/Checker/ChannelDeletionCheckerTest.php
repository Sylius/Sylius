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

namespace Tests\Sylius\Component\Channel\Checker;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Channel\Checker\ChannelDeletionChecker;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;

final class ChannelDeletionCheckerTest extends TestCase
{
    /** @var MockObject&ChannelRepositoryInterface<ChannelInterface> */
    private ChannelRepositoryInterface $channelRepository;

    private ChannelDeletionChecker $checker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->channelRepository = $this->createMock(ChannelRepositoryInterface::class);
        $this->checker = new ChannelDeletionChecker($this->channelRepository);
    }

    public function testShouldReturnsTrueWhenChannelIsDisabled(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $channel->expects(self::once())
            ->method('isEnabled')
            ->willReturn(false);

        self::assertTrue($this->checker->isDeletable($channel));
    }

    public function testShouldReturnsTrueWhenAtLeastTwoChannelsAreEnabled(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $anotherChannel = $this->createMock(ChannelInterface::class);

        $channel->expects(self::once())
            ->method('isEnabled')
            ->willReturn(true);
        $this->channelRepository->expects(self::once())
            ->method('findEnabled')
            ->willReturn([$channel, $anotherChannel]);

        self::assertTrue($this->checker->isDeletable($channel));
    }

    public function testShouldReturnsFalseWhenOnlyOneChannelIsEnabled(): void
    {
        $channel = $this->createMock(ChannelInterface::class);

        $channel->expects(self::once())
            ->method('isEnabled')
            ->willReturn(true);
        $this->channelRepository->expects(self::once())
            ->method('findEnabled')
            ->willReturn([$channel]);

        self::assertFalse($this->checker->isDeletable($channel));
    }

    public function testShouldReturnsFalseWhenNoChannelIsEnabled(): void
    {
        $channel = $this->createMock(ChannelInterface::class);

        $channel->expects(self::once())
            ->method('isEnabled')
            ->willReturn(true);
        $this->channelRepository->expects(self::once())
            ->method('findEnabled')
            ->willReturn([]);

        self::assertFalse($this->checker->isDeletable($channel));
    }
}
