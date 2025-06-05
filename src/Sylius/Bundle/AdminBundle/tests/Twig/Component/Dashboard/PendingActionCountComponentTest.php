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

namespace Tests\Sylius\Bundle\AdminBundle\Twig\Component\Dashboard;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\AdminBundle\PendingAction\Provider\PendingActionCountProviderInterface;
use Sylius\Bundle\AdminBundle\Twig\Component\Dashboard\PendingActionCountComponent;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;

final class PendingActionCountComponentTest extends TestCase
{
    private ChannelRepositoryInterface&MockObject $channelRepository;

    private MockObject&PendingActionCountProviderInterface $pendingActionCountProvider;

    private PendingActionCountComponent $pendingActionCountComponent;

    protected function setUp(): void
    {
        $this->channelRepository = $this->createMock(ChannelRepositoryInterface::class);
        $this->pendingActionCountProvider = $this->createMock(PendingActionCountProviderInterface::class);
        $this->pendingActionCountComponent = new PendingActionCountComponent(
            $this->channelRepository,
            $this->pendingActionCountProvider,
        );
    }

    public function testGetCountFromProvider(): void
    {
        $channelCode = 'WEB';
        $channel = $this->createMock(ChannelInterface::class);

        $this->channelRepository
            ->expects($this->once())
            ->method('findOneByCode')
            ->with($channelCode)
            ->willReturn($channel)
        ;

        $this->pendingActionCountProvider
            ->expects($this->once())
            ->method('count')
            ->with($channel)
            ->willReturn(12)
        ;

        $this->pendingActionCountComponent->channelCode = $channelCode;

        $this->assertSame(12, $this->pendingActionCountComponent->getCount());
    }

    public function testGetChannel(): void
    {
        $channelCode = 'WEB';
        $channel = $this->createMock(ChannelInterface::class);

        $this->channelRepository
            ->expects($this->once())
            ->method('findOneByCode')
            ->with($channelCode)
            ->willReturn($channel)
        ;

        $this->pendingActionCountComponent->channelCode = $channelCode;

        $this->assertSame($channel, $this->pendingActionCountComponent->getChannel());
    }

    public function testThrowExceptionIfChannelNotFound(): void
    {
        $channelCode = 'INVALID-CODE';

        $this->channelRepository
            ->expects($this->once())
            ->method('findOneByCode')
            ->with($channelCode)
            ->willReturn(null)
        ;

        $this->pendingActionCountComponent->channelCode = $channelCode;

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('Channel with code "%s" does not exist.', $channelCode));
        $this->pendingActionCountComponent->getChannel();
    }

    public function testUpdateChannelCode(): void
    {
        $this->pendingActionCountComponent->changeChannel('MOBILE');

        $this->assertSame('MOBILE', $this->pendingActionCountComponent->channelCode);
    }
}
