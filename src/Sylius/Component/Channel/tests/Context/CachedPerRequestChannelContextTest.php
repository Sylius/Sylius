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
use Sylius\Component\Channel\Context\CachedPerRequestChannelContext;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Channel\Model\ChannelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class CachedPerRequestChannelContextTest extends TestCase
{
    /** @var ChannelContextInterface&MockObject */
    private ChannelContextInterface $decoratedChannelContext;

    /** @var RequestStack&MockObject */
    private RequestStack $requestStack;

    private CachedPerRequestChannelContext $context;

    protected function setUp(): void
    {
        $this->decoratedChannelContext = $this->createMock(ChannelContextInterface::class);
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->context = new CachedPerRequestChannelContext(
            $this->decoratedChannelContext,
            $this->requestStack,
        );
    }

    public function testShouldImplementChannelContextInterface(): void
    {
        self::assertInstanceOf(ChannelContextInterface::class, $this->context);
    }

    public function testCachesChannelsForTheSameRequest(): void
    {
        $request = $this->createMock(Request::class);
        $channel = $this->createMock(ChannelInterface::class);

        $this->requestStack->expects(self::exactly(2))->method('getMainRequest')->willReturn($request);
        $this->decoratedChannelContext->expects(self::once())->method('getChannel')->willReturn($channel);

        self::assertSame($channel, $this->context->getChannel());
        self::assertSame($channel, $this->context->getChannel());
    }

    public function testDoesNotCacheChannelsForDifferentRequests(): void
    {
        $firstRequest = $this->createMock(Request::class);
        $secondRequest = $this->createMock(Request::class);
        $firstChannel = $this->createMock(ChannelInterface::class);
        $secondChannel = $this->createMock(ChannelInterface::class);

        $this->requestStack->expects(self::exactly(2))
            ->method('getMainRequest')
            ->willReturnOnConsecutiveCalls($firstRequest, $secondRequest);

        $this->decoratedChannelContext->expects(self::exactly(2))
            ->method('getChannel')
            ->willReturnOnConsecutiveCalls($firstChannel, $secondChannel);

        self::assertSame($firstChannel, $this->context->getChannel());
        self::assertSame($secondChannel, $this->context->getChannel());
    }

    public function testCachesChannelsForTheSameRequestEvenIfOtherRequestsInBetween(): void
    {
        $firstRequest = $this->createMock(Request::class);
        $secondRequest = $this->createMock(Request::class);
        $firstChannel = $this->createMock(ChannelInterface::class);
        $secondChannel = $this->createMock(ChannelInterface::class);

        $this->requestStack->expects(self::exactly(3))
            ->method('getMainRequest')
            ->willReturnOnConsecutiveCalls($firstRequest, $secondRequest, $firstRequest);

        $this->decoratedChannelContext->expects(self::exactly(2))
            ->method('getChannel')
            ->willReturnOnConsecutiveCalls($firstChannel, $secondChannel);

        self::assertSame($firstChannel, $this->context->getChannel());
        self::assertSame($secondChannel, $this->context->getChannel());
        self::assertSame($firstChannel, $this->context->getChannel());
    }

    public function testDoesNotCacheChannelsForNullMasterRequests(): void
    {
        $firstChannel = $this->createMock(ChannelInterface::class);
        $secondChannel = $this->createMock(ChannelInterface::class);

        $this->requestStack->expects(self::exactly(2))
            ->method('getMainRequest')
            ->willReturnOnConsecutiveCalls(null, null);

        $this->decoratedChannelContext->expects(self::exactly(2))
            ->method('getChannel')
            ->willReturnOnConsecutiveCalls($firstChannel, $secondChannel);

        self::assertSame($firstChannel, $this->context->getChannel());
        self::assertSame($secondChannel, $this->context->getChannel());
    }

    public function testCachesChannelNotFoundExceptionsForTheSameRequest(): void
    {
        $request = $this->createMock(Request::class);

        $this->requestStack->expects(self::exactly(2))->method('getMainRequest')->willReturn($request);

        $this->decoratedChannelContext->expects(self::once())
            ->method('getChannel')
            ->willThrowException(new ChannelNotFoundException());

        self::expectException(ChannelNotFoundException::class);

        try {
            $this->context->getChannel();
        } catch (ChannelNotFoundException) {
        }
        self::expectException(ChannelNotFoundException::class);
        $this->context->getChannel();
    }

    public function testDoesNotCacheChannelNotFoundExceptionsForNullMasterRequests(): void
    {
        $channel = $this->createMock(ChannelInterface::class);

        $this->requestStack->expects(self::exactly(2))
            ->method('getMainRequest')
            ->willReturnOnConsecutiveCalls(null, null);

        $callable = new class($channel) {
            private int $counter = 0;

            public function __construct(private readonly ChannelInterface $channel)
            {
            }

            public function __invoke(): ChannelInterface
            {
                if ($this->counter === 0) {
                    ++$this->counter;

                    throw new ChannelNotFoundException();
                }

                return $this->channel;
            }
        };

        $this->decoratedChannelContext->expects(self::exactly(2))
            ->method('getChannel')
            ->willReturnCallback($callable);

        try {
            $this->context->getChannel();
            self::fail('Expected ChannelNotFoundException was not thrown');
        } catch (ChannelNotFoundException) {
        }

        self::assertSame($channel, $this->context->getChannel());
    }
}
