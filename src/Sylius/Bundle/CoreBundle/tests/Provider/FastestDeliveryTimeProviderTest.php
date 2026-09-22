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

namespace Tests\Sylius\Bundle\CoreBundle\Provider;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Provider\FastestDeliveryTimeProvider;
use Sylius\Component\Channel\Model\ChannelInterface as BaseChannelInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;

#[AllowMockObjectsWithoutExpectations]
final class FastestDeliveryTimeProviderTest extends TestCase
{
    private MockObject&ShippingMethodRepositoryInterface $repository;

    private FastestDeliveryTimeProvider $provider;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(ShippingMethodRepositoryInterface::class);
        $this->provider = new FastestDeliveryTimeProvider($this->repository);
    }

    public function testReturnsNullWhenChannelIsNotCoreChannel(): void
    {
        /** @var BaseChannelInterface&MockObject $channel */
        $channel = $this->createMock(BaseChannelInterface::class);

        $result = $this->provider->provide($channel);

        $this->assertNull($result);
    }

    public function testReturnsNullWhenNoMethodsHaveDeliveryTime(): void
    {
        /** @var ChannelInterface&MockObject $channel */
        $channel = $this->createMock(ChannelInterface::class);

        $methodA = $this->createMock(ShippingMethodInterface::class);
        $methodA->method('getMinDeliveryTimeDays')->willReturn(null);
        $methodA->method('getMaxDeliveryTimeDays')->willReturn(null);

        $methodB = $this->createMock(ShippingMethodInterface::class);
        $methodB->method('getMinDeliveryTimeDays')->willReturn(null);
        $methodB->method('getMaxDeliveryTimeDays')->willReturn(null);

        $this->repository
            ->expects($this->once())
            ->method('findEnabledForChannel')
            ->with($channel)
            ->willReturn([$methodA, $methodB])
        ;

        $this->assertNull($this->provider->provide($channel));
    }

    public function testReturnsExactRangeWhenMinEqualsMax(): void
    {
        /** @var ChannelInterface&MockObject $channel */
        $channel = $this->createMock(ChannelInterface::class);

        $method = $this->createMock(ShippingMethodInterface::class);
        $method->method('getMinDeliveryTimeDays')->willReturn(3);
        $method->method('getMaxDeliveryTimeDays')->willReturn(3);

        $this->repository
            ->method('findEnabledForChannel')
            ->with($channel)
            ->willReturn([$method])
        ;

        $result = $this->provider->provide($channel);

        $this->assertNotNull($result);
        $this->assertSame(['minimumDays' => 3, 'maximumDays' => 3], $result);
    }

    public function testChoosesBestRangeByMaximumThenMinimum(): void
    {
        /** @var ChannelInterface&MockObject $channel */
        $channel = $this->createMock(ChannelInterface::class);

        $a = $this->createMock(ShippingMethodInterface::class);
        $a->method('getMinDeliveryTimeDays')->willReturn(2);
        $a->method('getMaxDeliveryTimeDays')->willReturn(5);

        $b = $this->createMock(ShippingMethodInterface::class);
        $b->method('getMinDeliveryTimeDays')->willReturn(1);
        $b->method('getMaxDeliveryTimeDays')->willReturn(4);

        $c = $this->createMock(ShippingMethodInterface::class);
        $c->method('getMinDeliveryTimeDays')->willReturn(3);
        $c->method('getMaxDeliveryTimeDays')->willReturn(4);

        $this->repository
            ->method('findEnabledForChannel')
            ->with($channel)
            ->willReturn([$a, $b, $c])
        ;

        $result = $this->provider->provide($channel);

        $this->assertNotNull($result);
        $this->assertSame(['minimumDays' => 1, 'maximumDays' => 4], $result);
    }

    public function testNormalizesSingleSidedRangeToExactValue(): void
    {
        /** @var ChannelInterface&MockObject $channel */
        $channel = $this->createMock(ChannelInterface::class);

        $onlyMin = $this->createMock(ShippingMethodInterface::class);
        $onlyMin->method('getMinDeliveryTimeDays')->willReturn(2);
        $onlyMin->method('getMaxDeliveryTimeDays')->willReturn(null);

        $onlyMax = $this->createMock(ShippingMethodInterface::class);
        $onlyMax->method('getMinDeliveryTimeDays')->willReturn(null);
        $onlyMax->method('getMaxDeliveryTimeDays')->willReturn(7);

        $this->repository
            ->method('findEnabledForChannel')
            ->with($channel)
            ->willReturn([$onlyMin, $onlyMax])
        ;

        $result = $this->provider->provide($channel);

        $this->assertNotNull($result);
        $this->assertSame(['minimumDays' => 2, 'maximumDays' => 2], $result);
    }
}
