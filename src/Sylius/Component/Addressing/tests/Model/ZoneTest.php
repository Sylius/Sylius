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
use Sylius\Component\Addressing\Model\Scope;
use Sylius\Component\Addressing\Model\Zone;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;

final class ZoneTest extends TestCase
{
    private Zone $zone;

    protected function setUp(): void
    {
        $this->zone = new Zone();
    }

    public function testImplementsSyliusZoneInterface(): void
    {
        self::assertInstanceOf(ZoneInterface::class, $this->zone);
    }

    public function testHasNoIdByDefault(): void
    {
        self::assertNull($this->zone->getId());
    }

    public function testHasNoNameByDefault(): void
    {
        self::assertNull($this->zone->getName());
    }

    public function testItsNameIsMutable(): void
    {
        $this->zone->setName('Yugoslavia');
        self::assertSame('Yugoslavia', $this->zone->getName());
    }

    public function testHasNoTypeByDefault(): void
    {
        self::assertNull($this->zone->getType());
    }

    public function testItsTypeIsMutable(): void
    {
        $this->zone->setType('country');
        self::assertSame('country', $this->zone->getType());
    }

    public function testHasNoMembersByDefault(): void
    {
        self::assertFalse($this->zone->hasMembers());
    }

    public function testAddsMember(): void
    {
        /** @var ZoneMemberInterface&MockObject $memberMock */
        $memberMock = $this->createMock(ZoneMemberInterface::class);
        $this->zone->addMember($memberMock);
        self::assertTrue($this->zone->hasMembers());
        self::assertTrue($this->zone->hasMember($memberMock));
    }

    public function testRemovesMember(): void
    {
        /** @var ZoneMemberInterface&MockObject $memberMock */
        $memberMock = $this->createMock(ZoneMemberInterface::class);
        $this->zone->addMember($memberMock);
        self::assertTrue($this->zone->hasMember($memberMock));
        $this->zone->removeMember($memberMock);
        self::assertFalse($this->zone->hasMember($memberMock));
    }

    public function testHasScopeAllByDefault(): void
    {
        self::assertSame(Scope::ALL, $this->zone->getScope());
    }

    public function testItsScopeIsMutable(): void
    {
        $this->zone->setScope('shipping');
        self::assertSame('shipping', $this->zone->getScope());
    }
}
