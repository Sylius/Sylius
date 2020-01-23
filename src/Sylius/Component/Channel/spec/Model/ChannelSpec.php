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

namespace spec\Sylius\Component\Channel\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Model\ChannelInterface;

final class ChannelSpec extends ObjectBehavior
{
    function it_implements_channel_interface(): void
    {
        $this->shouldImplement(ChannelInterface::class);
    }

    function it_has_no_id_by_default(): void
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_no_code_by_default(): void
    {
        $this->getCode()->shouldReturn(null);
    }

    function its_code_is_mutable(): void
    {
        $this->setCode('mobile');
        $this->getCode()->shouldReturn('mobile');
    }

    function it_is_unnamed_by_default(): void
    {
        $this->getName()->shouldReturn(null);
    }

    function its_name_is_mutable(): void
    {
        $this->setName('Mobile Store');
        $this->getName()->shouldReturn('Mobile Store');
    }

    function it_has_no_color_by_default(): void
    {
        $this->getColor()->shouldReturn(null);
    }

    function its_color_is_mutable(): void
    {
        $this->setColor('#1abb9c');
        $this->getColor()->shouldReturn('#1abb9c');
    }

    function it_is_enabled_by_default(): void
    {
        $this->shouldBeEnabled();
    }

    function it_can_be_disabled(): void
    {
        $this->setEnabled(false);
        $this->shouldNotBeEnabled();
    }

    function it_initializes_creation_date_by_default(): void
    {
        $this->getCreatedAt()->shouldHaveType(\DateTimeInterface::class);
    }

    function its_creation_date_is_mutable(\DateTime $date): void
    {
        $this->setCreatedAt($date);
        $this->getCreatedAt()->shouldReturn($date);
    }

    function it_has_no_last_update_date_by_default(): void
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }

    function its_last_update_date_is_mutable(\DateTime $date): void
    {
        $this->setUpdatedAt($date);
        $this->getUpdatedAt()->shouldReturn($date);
    }
}
