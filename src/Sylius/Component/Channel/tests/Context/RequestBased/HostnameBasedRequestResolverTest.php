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

namespace Tests\Sylius\Component\Channel\Context\RequestBased;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Channel\Context\RequestBased\HostnameBasedRequestResolver;
use Sylius\Component\Channel\Context\RequestBased\RequestResolverInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;

final class HostnameBasedRequestResolverTest extends TestCase
{
    /** @var MockObject&ChannelRepositoryInterface<ChannelInterface> */
    private ChannelRepositoryInterface $channelRepository;

    private HostnameBasedRequestResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->channelRepository = $this->createMock(ChannelRepositoryInterface::class);
        $this->resolver = new HostnameBasedRequestResolver($this->channelRepository);
    }

    public function testShouldImplementRequestResolverInterface(): void
    {
        self::assertInstanceOf(RequestResolverInterface::class, $this->resolver);
    }

    public function testFindsChannelByRequestHostname(): void
    {
        $request = $this->createMock(Request::class);
        $channel = $this->createMock(ChannelInterface::class);

        $request->expects(self::once())
            ->method('getHost')
            ->willReturn('example.org');

        $this->channelRepository->expects(self::once())
            ->method('findOneEnabledByHostname')
            ->with('example.org')
            ->willReturn($channel);

        self::assertSame($channel, $this->resolver->findChannel($request));
    }

    public function testShouldReturnsNullIfChannelWasNotFound(): void
    {
        $request = $this->createMock(Request::class);

        $request->expects(self::once())
            ->method('getHost')
            ->willReturn('example.org');

        $this->channelRepository->expects(self::once())
            ->method('findOneEnabledByHostname')
            ->with('example.org')
            ->willReturn(null);

        self::assertNull($this->resolver->findChannel($request));
    }
}
