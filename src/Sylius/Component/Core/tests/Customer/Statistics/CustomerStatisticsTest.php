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
use Sylius\Component\Core\Customer\Statistics\CustomerStatistics;
use Sylius\Component\Core\Customer\Statistics\PerChannelCustomerStatistics;
use Sylius\Component\Core\Model\ChannelInterface;

final class CustomerStatisticsTest extends TestCase
{
    private ChannelInterface&MockObject $channel;

    private PerChannelCustomerStatistics $firstStatistics;

    private PerChannelCustomerStatistics $secondStatistics;

    private CustomerStatistics $statistics;

    protected function setUp(): void
    {
        $this->channel = $this->createMock(ChannelInterface::class);
        $this->firstStatistics = new PerChannelCustomerStatistics(110, 120, $this->channel);
        $this->secondStatistics = new PerChannelCustomerStatistics(13, 120, $this->channel);
        $this->statistics = new CustomerStatistics([]);
    }

    public function testShouldThrowExceptionWhenArrayDoesNotContainOnlyPerChannelStatistics(): void
    {
        /** @var PerChannelCustomerStatistics[] $perChannelStatistics */
        $perChannelStatistics = [new \stdClass()];

        $this->expectException(\InvalidArgumentException::class);

        $this->statistics = new CustomerStatistics($perChannelStatistics);
    }

    public function testShouldReturnZeroIfThereAreNoPerChannelStatistics(): void
    {
        $this->assertSame(0, $this->statistics->getAllOrdersCount());
    }

    public function testShouldHaveNumberOfAllOrders(): void
    {
        $this->statistics = new CustomerStatistics([$this->firstStatistics, $this->secondStatistics]);

        $this->assertSame(123, $this->statistics->getAllOrdersCount());
    }

    public function testShouldHaveArrayOfStatisticsPerChannel(): void
    {
        $this->statistics = new CustomerStatistics([$this->firstStatistics, $this->secondStatistics]);

        $this->assertSame(
            [$this->firstStatistics, $this->secondStatistics],
            $this->statistics->getPerChannelsStatistics(),
        );
    }
}
