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

namespace spec\Sylius\Component\Grid\Definition;

use PhpSpec\ObjectBehavior;

final class ActionSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedThrough('fromNameAndType', ['view', 'link']);
    }

    function it_has_name(): void
    {
        $this->getName()->shouldReturn('view');
    }

    function it_has_type(): void
    {
        $this->getType()->shouldReturn('link');
    }

    function it_has_no_label_by_default(): void
    {
        $this->getLabel()->shouldReturn(null);
    }

    function its_label_is_mutable(): void
    {
        $this->setLabel('Read book');
        $this->getLabel()->shouldReturn('Read book');
    }

    function it_is_toggleable(): void
    {
        $this->isEnabled()->shouldReturn(true);

        $this->setEnabled(false);
        $this->isEnabled()->shouldReturn(false);
        $this->setEnabled(true);
        $this->isEnabled()->shouldReturn(true);
    }

    function it_has_no_icon_by_default(): void
    {
        $this->getIcon()->shouldReturn(null);
    }

    function its_icon_is_mutable(): void
    {
        $this->setIcon('checkmark');
        $this->getIcon()->shouldReturn('checkmark');
    }

    function it_has_no_options_by_default(): void
    {
        $this->getOptions()->shouldReturn([]);
    }

    function it_can_have_options(): void
    {
        $this->setOptions(['route' => 'sylius_admin_product_update']);
        $this->getOptions()->shouldReturn(['route' => 'sylius_admin_product_update']);
    }

    function it_has_last_position_by_default(): void
    {
        $this->getPosition()->shouldReturn(100);
    }

    function its_position_is_mutable(): void
    {
        $this->setPosition(1);
        $this->getPosition()->shouldReturn(1);
    }
}
