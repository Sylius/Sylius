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

class ChannelSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Channel\Model\Channel');
    }

    public function it_implements_Sylius_channel_interface()
    {
        $this->shouldImplement('Sylius\Component\Channel\Model\ChannelInterface');
    }

    public function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    public function it_has_no_code_by_default()
    {
        $this->getCode()->shouldReturn(null);
    }

    public function its_code_is_mutable()
    {
        $this->setCode('mobile');
        $this->getCode()->shouldReturn('mobile');
    }

    public function it_is_unnamed_by_default()
    {
        $this->getName()->shouldReturn(null);
    }

    public function its_name_is_mutable()
    {
        $this->setName('Mobile Store');
        $this->getName()->shouldReturn('Mobile Store');
    }

    public function it_has_no_color_by_default()
    {
        $this->getColor()->shouldReturn(null);
    }

    public function its_color_is_mutable()
    {
        $this->setColor('#1abb9c');
        $this->getColor()->shouldReturn('#1abb9c');
    }

    public function it_is_enabled_by_default()
    {
        $this->shouldBeEnabled();
    }

    public function it_can_be_disabled()
    {
        $this->setEnabled(false);
        $this->shouldNotBeEnabled();
    }

    public function it_initializes_creation_date_by_default()
    {
        $this->getCreatedAt()->shouldHaveType('DateTime');
    }

    public function its_creation_date_is_mutable()
    {
        $date = new \DateTime();

        $this->setCreatedAt($date);
        $this->getCreatedAt()->shouldReturn($date);
    }

    public function it_has_no_last_update_date_by_default()
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }

    public function its_last_update_date_is_mutable()
    {
        $date = new \DateTime();

        $this->setUpdatedAt($date);
        $this->getUpdatedAt()->shouldReturn($date);
    }
}
