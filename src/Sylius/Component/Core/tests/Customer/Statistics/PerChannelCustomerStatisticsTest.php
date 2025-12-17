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

namespace Tests\Sylius\Component\Core\Customer\Statistics;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Customer\Statistics\PerChannelCustomerStatistics;
use Sylius\Component\Core\Model\ChannelInterface;

final class PerChannelCustomerStatisticsTest extends TestCase
{
    private ChannelInterface&MockObject $channel;

    private PerChannelCustomerStatistics $statistics;

    protected function setUp(): void
    {
        $this->channel = $this->createMock(ChannelInterface::class);
        $this->statistics = new PerChannelCustomerStatistics(10, 20000, $this->channel);
    }

    public function testShouldHaveNumberOfOrders(): void
    {
        $this->assertSame(10, $this->statistics->getOrdersCount());
    }

    public function testShouldHaveCombinedValueOfAllOrders(): void
    {
        $this->assertSame(20000, $this->statistics->getOrdersValue());
    }

    public function testShouldHaveCloneOfTheOriginChannelOfOrders(): void
    {
        $this->assertEquals($this->channel, $this->statistics->getChannel());
    }

    public function testShouldHaveAverageValueOfOrder(): void
    {
        $this->assertEquals(2000, $this->statistics->getAverageOrderValue());
    }

    public function testShouldHaveZeroAverageOrderValueWhenOrderCountIsZero(): void
    {
        $this->statistics = new PerChannelCustomerStatistics(0, 0, $this->channel);

        $this->assertSame(0, $this->statistics->getAverageOrderValue());
    }
}
