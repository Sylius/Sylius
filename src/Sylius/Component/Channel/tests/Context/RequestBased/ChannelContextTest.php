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
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Channel\Context\RequestBased\ChannelContext;
use Sylius\Component\Channel\Context\RequestBased\RequestResolverInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class ChannelContextTest extends TestCase
{
    /** @var RequestResolverInterface&MockObject */
    private RequestResolverInterface $requestResolver;

    /** @var RequestStack&MockObject */
    private RequestStack $requestStack;

    private ChannelContext $context;

    protected function setUp(): void
    {
        $this->requestResolver = $this->createMock(RequestResolverInterface::class);
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->context = new ChannelContext(
            $this->requestResolver,
            $this->requestStack,
        );
    }

    public function testShouldImplementChannelContextInterface(): void
    {
        self::assertInstanceOf(ChannelContextInterface::class, $this->context);
    }

    public function testProxiesMasterRequestToRequestResolver(): void
    {
        $masterRequest = $this->createMock(Request::class);
        $channel = $this->createMock(ChannelInterface::class);

        $this->requestStack->expects(self::once())
            ->method('getMainRequest')
            ->willReturn($masterRequest);

        $this->requestResolver->expects(self::once())
            ->method('findChannel')
            ->with($masterRequest)
            ->willReturn($channel);

        self::assertSame($channel, $this->context->getChannel());
    }

    public function testShouldThrowsChannelNotFoundExceptionIfRequestResolverReturnsNull(): void
    {
        $masterRequest = $this->createMock(Request::class);

        $this->requestStack->expects(self::once())
            ->method('getMainRequest')
            ->willReturn($masterRequest);

        $this->requestResolver->expects(self::once())
            ->method('findChannel')
            ->with($masterRequest)
            ->willReturn(null);

        self::expectException(ChannelNotFoundException::class);

        $this->context->getChannel();
    }

    public function testShouldThrowsChannelNotFoundExceptionIfThereIsNoMasterRequest(): void
    {
        $this->requestStack->expects(self::once())
            ->method('getMainRequest')
            ->willReturn(null);

        self::expectException(ChannelNotFoundException::class);

        $this->context->getChannel();
    }
}
