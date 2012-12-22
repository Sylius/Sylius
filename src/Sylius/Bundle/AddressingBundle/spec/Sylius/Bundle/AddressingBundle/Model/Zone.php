<?php

namespace spec\Sylius\Bundle\AddressingBundle\Model;

use PHPSpec2\ObjectBehavior;

/**
 * Zone model spec.
 *
 * @author Саша Стаменковић <umpirsky@gmail.com>
 */
class Zone extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\AddressingBundle\Model\Zone');
    }

    function it_should_be_Sylius_zone()
    {
        $this->shouldImplement('Sylius\Bundle\AddressingBundle\Model\ZoneInterface');
    }

    function it_should_not_have_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_should_not_have_name_by_default()
    {
        $this->getName()->shouldReturn(null);
    }

    function its_name_should_be_mutable()
    {
        $this->setName('Yugoslavia');
        $this->getName()->shouldReturn('Yugoslavia');
    }

    function it_should_not_have_type_by_default()
    {
        $this->getType()->shouldReturn(null);
    }

    function its_type_should_be_mutable()
    {
        $this->setType('country');
        $this->getType()->shouldReturn('country');
    }

    function it_should_initialize_members_collection_by_default()
    {
        $this->getMembers()->shouldHaveType('Doctrine\Common\Collections\Collection');
    }

    function it_should_have_no_members_by_default()
    {
        $this->hasMembers()->shouldReturn(false);
    }

    /**
     * @param Doctrine\Common\Collections\Collection $members
     */
    function its_members_should_be_mutable($members)
    {
        $this->setMembers($members);
        $this->getMembers()->shouldReturn($members);
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\ZoneMemberInterface $member
     */
    function it_should_add_member_properly($member)
    {
        $this->addMember($member);
        $this->hasMembers()->shouldReturn(true);
        $this->hasMember($member)->shouldReturn(true);
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\ZoneMemberInterface $member
     */
    function it_should_remove_member_properly($member)
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
    function it_should_have_fluid_interface($member, $members)
    {
        $this->setName('Yugoslavia')->shouldReturn($this);
        $this->setMembers($members)->shouldReturn($this);
        $this->addMember($member)->shouldReturn($this);
        $this->removeMember($member)->shouldReturn($this);
    }
}
