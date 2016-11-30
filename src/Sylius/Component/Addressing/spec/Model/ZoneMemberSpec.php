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

use PhpSpec\ObjectBehavior;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Model\ZoneMember;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class ZoneMemberSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ZoneMember::class);
    }

    function it_implements_zone_member_interface()
    {
        $this->shouldImplement(ZoneMemberInterface::class);
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
        $this->setCode('IE');
        $this->getCode()->shouldReturn('IE');
    }

    function it_doesnt_belong_to_any_zone_by_default()
    {
        $this->getBelongsTo()->shouldReturn(null);
    }

    function it_can_belong_to_a_zone(ZoneInterface $zone)
    {
        $this->setBelongsTo($zone);
        $this->getBelongsTo()->shouldReturn($zone);
    }
}
