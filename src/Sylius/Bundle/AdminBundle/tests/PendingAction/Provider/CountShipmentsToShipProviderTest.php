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
use Sylius\Bundle\AdminBundle\PendingAction\Provider\CountShipmentsToShipProvider;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;

final class CountShipmentsToShipProviderTest extends TestCase
{
    private MockObject&ShipmentRepositoryInterface $shipmentRepository;

    private CountShipmentsToShipProvider $countShipmentsToShipProvider;

    protected function setUp(): void
    {
        $this->shipmentRepository = $this->createMock(ShipmentRepositoryInterface::class);
        $this->countShipmentsToShipProvider = new CountShipmentsToShipProvider($this->shipmentRepository);
    }

    public function testCountReadyShipmentsForGivenChannel(): void
    {
        $channel = $this->createMock(ChannelInterface::class);

        $this->shipmentRepository
            ->expects($this->once())
            ->method('countReadyByChannel')
            ->with($channel)
            ->willReturn(5)
        ;

        $this->assertSame(5, $this->countShipmentsToShipProvider->count($channel));
    }

    public function testThrowAnExceptionWhenChannelIsNull(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->countShipmentsToShipProvider->count();
    }
}
