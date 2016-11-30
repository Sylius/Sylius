<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Channel\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Model\Channel;
use Sylius\Component\Channel\Model\ChannelInterface;

final class ChannelSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Channel::class);
    }

    function it_implements_channel_interface()
    {
        $this->shouldImplement(ChannelInterface::class);
    }

    function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_no_code_by_default()
    {
        $this->getCode()->shouldReturn(null);
    }

    function its_code_is_mutable()
    {
        $this->setCode('mobile');
        $this->getCode()->shouldReturn('mobile');
    }

    function it_is_unnamed_by_default()
    {
        $this->getName()->shouldReturn(null);
    }

    function its_name_is_mutable()
    {
        $this->setName('Mobile Store');
        $this->getName()->shouldReturn('Mobile Store');
    }

    function it_has_no_color_by_default()
    {
        $this->getColor()->shouldReturn(null);
    }

    function its_color_is_mutable()
    {
        $this->setColor('#1abb9c');
        $this->getColor()->shouldReturn('#1abb9c');
    }

    function it_is_enabled_by_default()
    {
        $this->shouldBeEnabled();
    }

    function it_can_be_disabled()
    {
        $this->setEnabled(false);
        $this->shouldNotBeEnabled();
    }

    function it_initializes_creation_date_by_default()
    {
        $this->getCreatedAt()->shouldHaveType(\DateTime::class);
    }

    function its_creation_date_is_mutable(\DateTime $date)
    {
        $this->setCreatedAt($date);
        $this->getCreatedAt()->shouldReturn($date);
    }

    function it_has_no_last_update_date_by_default()
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }

    function its_last_update_date_is_mutable(\DateTime $date)
    {
        $this->setUpdatedAt($date);
        $this->getUpdatedAt()->shouldReturn($date);
    }
}
