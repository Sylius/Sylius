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
use Sylius\Bundle\AdminBundle\PendingAction\Provider\CountPendingPaymentsProvider;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;

final class CountPendingPaymentsProviderTest extends TestCase
{
    private MockObject&PaymentRepositoryInterface $paymentRepository;

    private CountPendingPaymentsProvider $countPendingPaymentsProvider;

    protected function setUp(): void
    {
        $this->paymentRepository = $this->createMock(PaymentRepositoryInterface::class);
        $this->countPendingPaymentsProvider = new CountPendingPaymentsProvider($this->paymentRepository);
    }

    public function testCountPendingPaymentsForGivenChannel(): void
    {
        $channel = $this->createMock(ChannelInterface::class);

        $this->paymentRepository
            ->expects($this->once())
            ->method('countNewByChannel')
            ->with($channel)
            ->willReturn(5)
        ;

        $this->assertSame(5, $this->countPendingPaymentsProvider->count($channel));
    }

    public function testThrowAnExceptionWhenChannelIsNull(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->countPendingPaymentsProvider->count();
    }
}
