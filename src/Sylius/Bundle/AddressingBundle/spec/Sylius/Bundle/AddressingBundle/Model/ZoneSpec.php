<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\AddressingBundle\Model;

use PhpSpec\ObjectBehavior;

/**
 * @author Саша Стаменковић <umpirsky@gmail.com>
 */
class ZoneSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\AddressingBundle\Model\Zone');
    }

    function it_implements_Sylius_zone_interface()
    {
        $this->shouldImplement('Sylius\Bundle\AddressingBundle\Model\ZoneInterface');
    }

    function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_no_name_by_default()
    {
        $this->getName()->shouldReturn(null);
    }

    function its_name_is_mutable()
    {
        $this->setName('Yugoslavia');
        $this->getName()->shouldReturn('Yugoslavia');
    }

    function it_has_no_type_by_default()
    {
        $this->getType()->shouldReturn(null);
    }

    function its_type_is_mutable()
    {
        $this->setType('country');
        $this->getType()->shouldReturn('country');
    }

    function it_initializes_members_collection_by_default()
    {
        $this->getMembers()->shouldHaveType('Doctrine\Common\Collections\Collection');
    }

    function it_has_no_members_by_default()
    {
        $this->hasMembers()->shouldReturn(false);
    }

    /**
     * @param Doctrine\Common\Collections\Collection $members
     */
    function its_members_are_mutable($members)
    {
        $this->setMembers($members);
        $this->getMembers()->shouldReturn($members);
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\ZoneMemberInterface $member
     */
    function it_adds_member($member)
    {
        $this->addMember($member);
        $this->hasMembers()->shouldReturn(true);
        $this->hasMember($member)->shouldReturn(true);
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\ZoneMemberInterface $member
     */
    function it_removes_member($member)
    {
        $this->addMember($member);
        $this->hasMember($member)->shouldReturn(true);

        $this->removeMember($member);
        $this->hasMember($member)->shouldReturn(false);
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\ZoneMemberInterface $member
     * @param Doctrine\Common\Collections\Collection                   $members
     */
    function it_has_fluent_interface($member, $members)
    {
        $this->setName('Yugoslavia')->shouldReturn($this);
        $this->setMembers($members)->shouldReturn($this);
        $this->addMember($member)->shouldReturn($this);
        $this->removeMember($member)->shouldReturn($this);
    }
}
