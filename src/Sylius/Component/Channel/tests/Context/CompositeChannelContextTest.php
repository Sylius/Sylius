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

use PHPUnit\Framework\TestCase;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Channel\Context\CompositeChannelContext;
use Sylius\Component\Channel\Model\ChannelInterface;

final class CompositeChannelContextTest extends TestCase
{
    private CompositeChannelContext $context;

    protected function setUp(): void
    {
        parent::setUp();
        $this->context = new CompositeChannelContext();
    }

    public function testShouldImplementChannelContextInterface(): void
    {
        self::assertInstanceOf(ChannelContextInterface::class, $this->context);
    }

    public function testThrowsExceptionIfNoNestedChannelContextsDefined(): void
    {
        self::expectException(ChannelNotFoundException::class);

        $this->context->getChannel();
    }

    public function testShouldThrowsExceptionIfNoneOfNestedChannelContextsReturnedAChannel(): void
    {
        $channelContext = $this->createMock(ChannelContextInterface::class);
        $channelContext->method('getChannel')->willThrowException(new ChannelNotFoundException());

        $this->context->addContext($channelContext);

        self::expectException(ChannelNotFoundException::class);

        $this->context->getChannel();
    }

    public function testShouldReturnsFirstResultReturnedByNestedRequestResolvers(): void
    {
        $firstChannelContext = $this->createMock(ChannelContextInterface::class);
        $secondChannelContext = $this->createMock(ChannelContextInterface::class);
        $thirdChannelContext = $this->createMock(ChannelContextInterface::class);
        $channel = $this->createMock(ChannelInterface::class);

        $firstChannelContext->method('getChannel')->willThrowException(new ChannelNotFoundException());
        $secondChannelContext->method('getChannel')->willReturn($channel);

        $this->context->addContext($firstChannelContext);
        $this->context->addContext($secondChannelContext);
        $this->context->addContext($thirdChannelContext);

        self::assertSame($channel, $this->context->getChannel());
    }

    public function testNestedRequestResolversShouldHavePriority(): void
    {
        $firstChannelContext = $this->createMock(ChannelContextInterface::class);
        $secondChannelContext = $this->createMock(ChannelContextInterface::class);
        $thirdChannelContext = $this->createMock(ChannelContextInterface::class);
        $channel = $this->createMock(ChannelInterface::class);

        $secondChannelContext->method('getChannel')->willReturn($channel);
        $thirdChannelContext->method('getChannel')->willThrowException(new ChannelNotFoundException());

        $this->context->addContext($firstChannelContext, -5);
        $this->context->addContext($secondChannelContext, 0);
        $this->context->addContext($thirdChannelContext, 5);

        self::assertSame($channel, $this->context->getChannel());
    }
}
