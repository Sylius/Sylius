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

use PhpSpec\ObjectBehavior;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Model\ZoneMember;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;

final class ZoneMemberSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ZoneMember::class);
    }

    public function it_implements_zone_member_interface(): void
    {
        $this->shouldImplement(ZoneMemberInterface::class);
    }

    public function it_has_no_id_by_default(): void
    {
        $this->getId()->shouldReturn(null);
    }

    public function it_has_no_code_by_default(): void
    {
        $this->getCode()->shouldReturn(null);
    }

    public function its_code_is_mutable(): void
    {
        $this->setCode('IE');
        $this->getCode()->shouldReturn('IE');
    }

    public function it_doesnt_belong_to_any_zone_by_default(): void
    {
        $this->getBelongsTo()->shouldReturn(null);
    }

    public function it_can_belong_to_a_zone(ZoneInterface $zone): void
    {
        $this->setBelongsTo($zone);
        $this->getBelongsTo()->shouldReturn($zone);
    }
}
