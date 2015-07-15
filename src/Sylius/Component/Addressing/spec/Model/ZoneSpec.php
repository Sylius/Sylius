<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Addressing\Model;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class ZoneSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Addressing\Model\Zone');
    }

    public function it_implements_Sylius_zone_interface()
    {
        $this->shouldImplement('Sylius\Component\Addressing\Model\ZoneInterface');
    }

    public function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    public function it_has_no_name_by_default()
    {
        $this->getName()->shouldReturn(null);
    }

    public function its_name_is_mutable()
    {
        $this->setName('Yugoslavia');
        $this->getName()->shouldReturn('Yugoslavia');
    }

    public function it_has_no_type_by_default()
    {
        $this->getType()->shouldReturn(null);
    }

    public function its_type_is_mutable()
    {
        $this->setType('country');
        $this->getType()->shouldReturn('country');
    }

    public function it_initializes_members_collection_by_default()
    {
        $this->getMembers()->shouldHaveType('Doctrine\Common\Collections\Collection');
    }

    public function it_has_no_members_by_default()
    {
        $this->hasMembers()->shouldReturn(false);
    }

    public function its_members_are_mutable(Collection $members)
    {
        $this->setMembers($members);
        $this->getMembers()->shouldReturn($members);
    }

    public function it_adds_member(ZoneMemberInterface $member)
    {
        $this->addMember($member);
        $this->hasMembers()->shouldReturn(true);
        $this->hasMember($member)->shouldReturn(true);
    }

    public function it_removes_member(ZoneMemberInterface $member)
    {
        $this->addMember($member);
        $this->hasMember($member)->shouldReturn(true);

        $this->removeMember($member);
        $this->hasMember($member)->shouldReturn(false);
    }

    public function it_has_no_scope_by_default()
    {
        $this->getScope()->shouldReturn(null);
    }

    public function its_scope_is_mutable()
    {
        $this->setScope('shipping');
        $this->getScope()->shouldReturn('shipping');
    }

    public function it_has_fluent_interface(ZoneMemberInterface $member, Collection $members)
    {
        $this->setName('Yugoslavia')->shouldReturn($this);
        $this->setMembers($members)->shouldReturn($this);
        $this->addMember($member)->shouldReturn($this);
        $this->removeMember($member)->shouldReturn($this);
        $this->setScope('shipping')->shouldReturn($this);
    }
}
