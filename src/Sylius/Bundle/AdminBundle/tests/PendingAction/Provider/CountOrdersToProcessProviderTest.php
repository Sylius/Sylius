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

namespace Tests\Sylius\Bundle\AdminBundle\PendingAction\Provider;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\AdminBundle\PendingAction\Provider\CountOrdersToProcessProvider;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;

final class CountOrdersToProcessProviderTest extends TestCase
{
    private MockObject&OrderRepositoryInterface $orderRepository;

    private CountOrdersToProcessProvider $countOrdersToProcessProvider;

    protected function setUp(): void
    {
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->countOrdersToProcessProvider = new CountOrdersToProcessProvider($this->orderRepository);
    }

    public function testCountNewOrdersForGivenChannel(): void
    {
        $channel = $this->createMock(ChannelInterface::class);

        $this->orderRepository
            ->expects($this->once())
            ->method('countNewByChannel')
            ->with($channel)
            ->willReturn(5)
        ;

        $this->assertSame(5, $this->countOrdersToProcessProvider->count($channel));
    }

    public function testThrowAnExceptionWhenChannelIsNull(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->countOrdersToProcessProvider->count();
    }
}
