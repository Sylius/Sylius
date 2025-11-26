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

namespace Tests\Sylius\Component\Addressing\Matcher;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Addressing\Matcher\ZoneMatcher;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Addressing\Model\Scope;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Repository\ZoneRepositoryInterface;

final class ZoneMatcherTest extends TestCase
{
    /** @var ZoneRepositoryInterface<ZoneInterface>&MockObject */
    private MockObject $zoneRepositoryMock;

    private ZoneMatcher $zoneMatcher;

    protected function setUp(): void
    {
        $this->zoneRepositoryMock = $this->createMock(ZoneRepositoryInterface::class);
        $this->zoneMatcher = new ZoneMatcher($this->zoneRepositoryMock);
    }

    public function testReturnsAMatchingZoneByProvince(): void
    {
        /** @var AddressInterface&MockObject $addressMock */
        $addressMock = $this->createMock(AddressInterface::class);
        /** @var ZoneInterface&MockObject $zoneMock */
        $zoneMock = $this->createMock(ZoneInterface::class);
        $this->zoneRepositoryMock->expects(self::once())->method('findOneByAddressAndType')->with($addressMock, ZoneInterface::TYPE_PROVINCE, null)->willReturn($zoneMock);
        self::assertSame($zoneMock, $this->zoneMatcher->match($addressMock));
    }

    public function testReturnsAMatchingZoneByCountry(): void
    {
        /** @var AddressInterface&MockObject $addressMock */
        $addressMock = $this->createMock(AddressInterface::class);
        /** @var ZoneInterface&MockObject $zoneMock */
        $zoneMock = $this->createMock(ZoneInterface::class);
        $this->zoneRepositoryMock
            ->expects(self::exactly(2))
            ->method('findOneByAddressAndType')
            ->willReturnCallback(fn (AddressInterface $address, string $type, ?string $scope = null) => match ([$address, $type, $scope]) {
                [$addressMock, ZoneInterface::TYPE_PROVINCE, null] => null,
                [$addressMock, ZoneInterface::TYPE_COUNTRY, null] => $zoneMock,
                default => throw new \UnhandledMatchError(),
            });
        self::assertSame($zoneMock, $this->zoneMatcher->match($addressMock));
    }

    public function testReturnsAMatchingZoneByMember(): void
    {
        /** @var AddressInterface&MockObject $addressMock */
        $addressMock = $this->createMock(AddressInterface::class);
        /** @var ZoneInterface&MockObject $zoneMock */
        $zoneMock = $this->createMock(ZoneInterface::class);
        $this->zoneRepositoryMock
            ->expects(self::exactly(3))
            ->method('findOneByAddressAndType')
            ->willReturnCallback(fn (AddressInterface $address, string $type, ?string $scope = null) => match ([$address, $type, $scope]) {
                [$addressMock, ZoneInterface::TYPE_PROVINCE, null],
                [$addressMock, ZoneInterface::TYPE_COUNTRY, null] => null,
                [$addressMock, ZoneInterface::TYPE_ZONE, null] => $zoneMock,
                default => throw new \UnhandledMatchError(),
            });
        self::assertSame($zoneMock, $this->zoneMatcher->match($addressMock));
    }

    public function testReturnsNullIfNoMatchingZoneFound(): void
    {
        /** @var AddressInterface&MockObject $addressMock */
        $addressMock = $this->createMock(AddressInterface::class);
        $this->zoneRepositoryMock
            ->expects(self::exactly(3))
            ->method('findOneByAddressAndType')
            ->willReturn(null);
        self::assertNull($this->zoneMatcher->match($addressMock));
    }

    public function testReturnsAllMatchingZones(): void
    {
        /** @var AddressInterface&MockObject $addressMock */
        $addressMock = $this->createMock(AddressInterface::class);
        /** @var ZoneInterface&MockObject $zoneOneMock */
        $zoneOneMock = $this->createMock(ZoneInterface::class);
        /** @var ZoneInterface&MockObject $zoneTwoMock */
        $zoneTwoMock = $this->createMock(ZoneInterface::class);
        /** @var ZoneInterface&MockObject $zoneThreeMock */
        $zoneThreeMock = $this->createMock(ZoneInterface::class);
        $this->zoneRepositoryMock->expects(self::once())->method('findByAddress')->with($addressMock)->willReturn([$zoneOneMock]);
        $this->zoneRepositoryMock
            ->expects(self::exactly(3))
            ->method('findByMembers')
            ->willReturnCallback(fn (array $members) => match ($members) {
                [$zoneOneMock] => [$zoneTwoMock],
                [$zoneTwoMock] => [$zoneThreeMock],
                [$zoneThreeMock] => [],
                default => throw new \UnhandledMatchError(),
            });
        $matchedZones = $this->zoneMatcher->matchAll($addressMock);
        self::assertCount(3, $matchedZones);
        self::assertSame([$zoneOneMock, $zoneTwoMock, $zoneThreeMock], $matchedZones);
    }

    public function testReturnsAllMatchingZonesWithinAMatchingScope(): void
    {
        /** @var AddressInterface&MockObject $addressMock */
        $addressMock = $this->createMock(AddressInterface::class);
        /** @var ZoneInterface&MockObject $zoneOneMock */
        $zoneOneMock = $this->createMock(ZoneInterface::class);
        /** @var ZoneInterface&MockObject $zoneTwoMock */
        $zoneTwoMock = $this->createMock(ZoneInterface::class);
        /** @var ZoneInterface&MockObject $zoneThreeMock */
        $zoneThreeMock = $this->createMock(ZoneInterface::class);
        $zoneOneMock->expects(self::atLeastOnce())->method('getScope')->willReturn('shipping');
        $zoneTwoMock->expects(self::atLeastOnce())->method('getScope')->willReturn(Scope::ALL);
        $zoneThreeMock->expects(self::atLeastOnce())->method('getScope')->willReturn('custom');
        $this->zoneRepositoryMock->expects(self::once())->method('findByAddress')->with($addressMock)->willReturn([$zoneOneMock]);
        $this->zoneRepositoryMock
            ->expects(self::exactly(3))
            ->method('findByMembers')
            ->willReturnCallback(fn (array $members) => match ($members) {
                [$zoneOneMock] => [$zoneTwoMock],
                [$zoneTwoMock] => [$zoneThreeMock],
                [$zoneThreeMock] => [],
                default => throw new \UnhandledMatchError(),
            });
        $matchedZones = $this->zoneMatcher->matchAll($addressMock, 'shipping');
        self::assertCount(2, $matchedZones);
        self::assertSame([$zoneOneMock, $zoneTwoMock], $matchedZones);
    }
}
