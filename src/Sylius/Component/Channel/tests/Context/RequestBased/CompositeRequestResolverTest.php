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

use PHPUnit\Framework\TestCase;
use Sylius\Component\Channel\Context\RequestBased\CompositeRequestResolver;
use Sylius\Component\Channel\Context\RequestBased\RequestResolverInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Symfony\Component\HttpFoundation\Request;

final class CompositeRequestResolverTest extends TestCase
{
    private CompositeRequestResolver $resolver;

    protected function setUp(): void
    {
        $this->resolver = new CompositeRequestResolver();
    }

    public function testShouldImplementRequestResolverInterface(): void
    {
        self::assertInstanceOf(RequestResolverInterface::class, $this->resolver);
    }

    public function testShouldReturnsNullIfThereAreNoNestedRequestResolversAdded(): void
    {
        $request = $this->createMock(Request::class);

        self::assertNull($this->resolver->findChannel($request));
    }

    public function testShouldReturnsNullIfNoneOfNestedRequestResolversReturnedChannel(): void
    {
        $request = $this->createMock(Request::class);
        $nestedResolver = $this->createMock(RequestResolverInterface::class);

        $nestedResolver->expects(self::once())
            ->method('findChannel')
            ->with($request)
            ->willReturn(null);

        $this->resolver->addResolver($nestedResolver);

        self::assertNull($this->resolver->findChannel($request));
    }

    public function testShouldReturnsFirstResultReturnedByNestedRequestResolvers(): void
    {
        $request = $this->createMock(Request::class);
        $firstResolver = $this->createMock(RequestResolverInterface::class);
        $secondResolver = $this->createMock(RequestResolverInterface::class);
        $thirdResolver = $this->createMock(RequestResolverInterface::class);
        $channel = $this->createMock(ChannelInterface::class);

        $firstResolver->expects(self::once())->method('findChannel')->with($request)->willReturn(null);
        $secondResolver->expects(self::once())->method('findChannel')->with($request)->willReturn($channel);

        $thirdResolver->expects(self::never())->method('findChannel');

        $this->resolver->addResolver($firstResolver);
        $this->resolver->addResolver($secondResolver);
        $this->resolver->addResolver($thirdResolver);

        self::assertSame($channel, $this->resolver->findChannel($request));
    }

    public function testNestedRequestResolversCanHavePriority(): void
    {
        $request = $this->createMock(Request::class);
        $firstResolver = $this->createMock(RequestResolverInterface::class);
        $secondResolver = $this->createMock(RequestResolverInterface::class);
        $thirdResolver = $this->createMock(RequestResolverInterface::class);
        $channel = $this->createMock(ChannelInterface::class);

        $firstResolver->expects(self::never())->method('findChannel');
        $secondResolver->expects(self::once())->method('findChannel')->with($request)->willReturn($channel);
        $thirdResolver->expects(self::once())->method('findChannel')->with($request)->willReturn(null);

        $this->resolver->addResolver($firstResolver, -5);
        $this->resolver->addResolver($secondResolver, 0);
        $this->resolver->addResolver($thirdResolver, 5);

        self::assertSame($channel, $this->resolver->findChannel($request));
    }
}
