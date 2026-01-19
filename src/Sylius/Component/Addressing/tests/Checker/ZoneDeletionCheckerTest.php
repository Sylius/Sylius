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

namespace Tests\Sylius\Component\Addressing\Checker;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Addressing\Checker\ZoneDeletionChecker;
use Sylius\Component\Addressing\Checker\ZoneDeletionCheckerInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

final class ZoneDeletionCheckerTest extends TestCase
{
    /** @var RepositoryInterface<ZoneMemberInterface>&MockObject */
    private MockObject $zoneMemberRepositoryMock;

    private ZoneDeletionChecker $zoneDeletionChecker;

    protected function setUp(): void
    {
        $this->zoneMemberRepositoryMock = $this->createMock(RepositoryInterface::class);
        $this->zoneDeletionChecker = new ZoneDeletionChecker($this->zoneMemberRepositoryMock);
    }

    public function testImplementsZoneDeletionCheckerInterface(): void
    {
        self::assertInstanceOf(ZoneDeletionCheckerInterface::class, $this->zoneDeletionChecker);
    }

    public function testSaysZoneIsNotDeletableIfTheZoneExistsAsAZoneMember(): void
    {
        /** @var ZoneInterface&MockObject $zoneMock */
        $zoneMock = $this->createMock(ZoneInterface::class);
        /** @var ZoneMemberInterface&MockObject $zoneMemberMock */
        $zoneMemberMock = $this->createMock(ZoneMemberInterface::class);
        $zoneMock->expects(self::once())->method('getCode')->willReturn('US');
        $this->zoneMemberRepositoryMock->expects(self::once())->method('findOneBy')->with(['code' => 'US'])->willReturn($zoneMemberMock);
        self::assertFalse($this->zoneDeletionChecker->isDeletable($zoneMock));
    }

    public function testSaysZoneIsNotDeletableIfTheZoneDoesNotExistAsAZoneMember(): void
    {
        /** @var ZoneInterface&MockObject $zoneMock */
        $zoneMock = $this->createMock(ZoneInterface::class);
        $zoneMock->expects(self::once())->method('getCode')->willReturn('US');
        $this->zoneMemberRepositoryMock->expects(self::once())->method('findOneBy')->with(['code' => 'US'])->willReturn(null);
        self::assertTrue($this->zoneDeletionChecker->isDeletable($zoneMock));
    }
}
