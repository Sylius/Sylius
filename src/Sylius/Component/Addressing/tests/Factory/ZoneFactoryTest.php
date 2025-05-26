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

namespace Tests\Sylius\Component\Addressing\Factory;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Addressing\Factory\ZoneFactory;
use Sylius\Component\Addressing\Factory\ZoneFactoryInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;
use Sylius\Resource\Factory\FactoryInterface;

final class ZoneFactoryTest extends TestCase
{
    /** @var FactoryInterface<ZoneInterface>&MockObject */
    private MockObject $factoryMock;

    /** @var FactoryInterface<ZoneMemberInterface>&MockObject */
    private MockObject $zoneMemberFactoryMock;

    /** @var ZoneFactory<ZoneInterface> */
    private ZoneFactory $zoneFactory;

    protected function setUp(): void
    {
        $this->factoryMock = $this->createMock(FactoryInterface::class);
        $this->zoneMemberFactoryMock = $this->createMock(FactoryInterface::class);
        $this->zoneFactory = new ZoneFactory($this->factoryMock, $this->zoneMemberFactoryMock);
    }

    public function testImplementsFactoryInterface(): void
    {
        self::assertInstanceOf(FactoryInterface::class, $this->zoneFactory);
    }

    public function testImplementsZoneFactoryInterface(): void
    {
        self::assertInstanceOf(ZoneFactoryInterface::class, $this->zoneFactory);
    }

    public function testCreatesZoneWithType(): void
    {
        /** @var ZoneInterface&MockObject $zoneMock */
        $zoneMock = $this->createMock(ZoneInterface::class);
        $this->factoryMock->expects(self::once())->method('createNew')->willReturn($zoneMock);
        $zoneMock->expects(self::once())->method('setType')->with('country');
        self::assertSame($zoneMock, $this->zoneFactory->createTyped('country'));
    }

    public function testCreatesZoneWithMembers(): void
    {
        /** @var ZoneInterface&MockObject $zoneMock */
        $zoneMock = $this->createMock(ZoneInterface::class);
        /** @var ZoneMemberInterface&MockObject $zoneMember1Mock */
        $zoneMember1Mock = $this->createMock(ZoneMemberInterface::class);
        /** @var ZoneMemberInterface&MockObject $zoneMember2Mock */
        $zoneMember2Mock = $this->createMock(ZoneMemberInterface::class);
        $this->factoryMock->expects(self::once())->method('createNew')->willReturn($zoneMock);
        $this->zoneMemberFactoryMock
            ->expects($this->exactly(2))
            ->method('createNew')
            ->willReturnOnConsecutiveCalls($zoneMember1Mock, $zoneMember2Mock);
        $zoneMember1Mock->expects(self::once())->method('setCode')->with('GB');
        $zoneMember2Mock->expects(self::once())->method('setCode')->with('PL');
        $zoneMock->expects($this->exactly(2))->method('addMember')->willReturnMap([[$zoneMember1Mock], [$zoneMember2Mock]]);
        self::assertSame($zoneMock, $this->zoneFactory->createWithMembers(['GB', 'PL']));
    }
}
