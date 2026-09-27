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

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Channel\Context\RequestBased\HostnameBasedRequestResolver;
use Sylius\Component\Channel\Context\RequestBased\RequestResolverInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;

#[AllowMockObjectsWithoutExpectations]
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

    public function testFindsChannelByLocalhostWhenRequestIs127001(): void
    {
        $request = $this->createMock(Request::class);
        $channel = $this->createMock(ChannelInterface::class);

        $request->expects(self::once())
            ->method('getHost')
            ->willReturn('127.0.0.1');

        $this->channelRepository->expects(self::exactly(2))
            ->method('findOneEnabledByHostname')
            ->willReturnCallback(function (string $hostname) use ($channel) {
                return match ($hostname) {
                    '127.0.0.1' => null,
                    'localhost' => $channel,
                    default => null,
                };
            });

        self::assertSame($channel, $this->resolver->findChannel($request));
    }

    public function testFindsChannelBy127001WhenRequestIsLocalhost(): void
    {
        $request = $this->createMock(Request::class);
        $channel = $this->createMock(ChannelInterface::class);

        $request->expects(self::once())
            ->method('getHost')
            ->willReturn('localhost');

        $this->channelRepository->expects(self::exactly(2))
            ->method('findOneEnabledByHostname')
            ->willReturnCallback(function (string $hostname) use ($channel) {
                return match ($hostname) {
                    'localhost' => null,
                    '127.0.0.1' => $channel,
                    default => null,
                };
            });

        self::assertSame($channel, $this->resolver->findChannel($request));
    }

    public function testFindsChannelByIpv6LocalhostWhenRequestIsLocalhost(): void
    {
        $request = $this->createMock(Request::class);
        $channel = $this->createMock(ChannelInterface::class);

        $request->expects(self::once())
            ->method('getHost')
            ->willReturn('localhost');

        $this->channelRepository->expects(self::exactly(3))
            ->method('findOneEnabledByHostname')
            ->willReturnCallback(function (string $hostname) use ($channel) {
                return match ($hostname) {
                    'localhost' => null,
                    '127.0.0.1' => null,
                    '::1' => $channel,
                    default => null,
                };
            });

        self::assertSame($channel, $this->resolver->findChannel($request));
    }

    public function testReturnsNullWhenNoLocalhostEquivalentFound(): void
    {
        $request = $this->createMock(Request::class);

        $request->expects(self::once())
            ->method('getHost')
            ->willReturn('localhost');

        $this->channelRepository->expects(self::exactly(3))
            ->method('findOneEnabledByHostname')
            ->willReturn(null);

        self::assertNull($this->resolver->findChannel($request));
    }

    public function testPrefersExactMatchOverLocalhostEquivalent(): void
    {
        $request = $this->createMock(Request::class);
        $exactChannel = $this->createMock(ChannelInterface::class);

        $request->expects(self::once())
            ->method('getHost')
            ->willReturn('127.0.0.1');

        $this->channelRepository->expects(self::once())
            ->method('findOneEnabledByHostname')
            ->with('127.0.0.1')
            ->willReturn($exactChannel);

        self::assertSame($exactChannel, $this->resolver->findChannel($request));
    }
}
