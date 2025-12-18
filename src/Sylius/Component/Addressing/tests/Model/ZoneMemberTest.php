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

namespace Tests\Sylius\Component\Addressing\Model;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Model\ZoneMember;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;

final class ZoneMemberTest extends TestCase
{
    private ZoneMember $zoneMember;

    protected function setUp(): void
    {
        $this->zoneMember = new ZoneMember();
    }

    public function testImplementsZoneMemberInterface(): void
    {
        self::assertInstanceOf(ZoneMemberInterface::class, $this->zoneMember);
    }

    public function testHasNoIdByDefault(): void
    {
        self::assertNull($this->zoneMember->getId());
    }

    public function testHasNoCodeByDefault(): void
    {
        self::assertNull($this->zoneMember->getCode());
    }

    public function testItsCodeIsMutable(): void
    {
        $this->zoneMember->setCode('IE');
        self::assertSame('IE', $this->zoneMember->getCode());
    }

    public function testDoesntBelongToAnyZoneByDefault(): void
    {
        self::assertNull($this->zoneMember->getBelongsTo());
    }

    public function testCanBelongToAZone(): void
    {
        /** @var ZoneInterface&MockObject $zoneMock */
        $zoneMock = $this->createMock(ZoneInterface::class);
        $this->zoneMember->setBelongsTo($zoneMock);
        self::assertSame($zoneMock, $this->zoneMember->getBelongsTo());
    }
}
