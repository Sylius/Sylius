<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Addressing\Model;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Addressing\Model\Scope;
use Sylius\Component\Addressing\Model\Zone;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;

final class ZoneSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(Zone::class);
    }

    public function it_implements_Sylius_zone_interface(): void
    {
        $this->shouldImplement(ZoneInterface::class);
    }

    public function it_has_no_id_by_default(): void
    {
        $this->getId()->shouldReturn(null);
    }

    public function it_has_no_name_by_default(): void
    {
        $this->getName()->shouldReturn(null);
    }

    public function its_name_is_mutable(): void
    {
        $this->setName('Yugoslavia');
        $this->getName()->shouldReturn('Yugoslavia');
    }

    public function it_has_no_type_by_default(): void
    {
        $this->getType()->shouldReturn(null);
    }

    public function its_type_is_mutable(): void
    {
        $this->setType('country');
        $this->getType()->shouldReturn('country');
    }

    public function it_initializes_members_collection_by_default(): void
    {
        $this->getMembers()->shouldHaveType(Collection::class);
    }

    public function it_has_no_members_by_default(): void
    {
        $this->hasMembers()->shouldReturn(false);
    }

    public function it_adds_member(ZoneMemberInterface $member): void
    {
        $this->addMember($member);
        $this->hasMembers()->shouldReturn(true);
        $this->hasMember($member)->shouldReturn(true);
    }

    public function it_removes_member(ZoneMemberInterface $member): void
    {
        $this->addMember($member);
        $this->hasMember($member)->shouldReturn(true);

        $this->removeMember($member);
        $this->hasMember($member)->shouldReturn(false);
    }

    public function it_has_scope_all_by_default(): void
    {
        $this->getScope()->shouldReturn(Scope::ALL);
    }

    public function its_scope_is_mutable(): void
    {
        $this->setScope('shipping');
        $this->getScope()->shouldReturn('shipping');
    }
}
