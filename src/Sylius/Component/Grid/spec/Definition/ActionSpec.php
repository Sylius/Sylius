<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Grid\Definition;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Grid\Definition\Action;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class ActionSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedThrough('fromNameAndType', ['view', 'link']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Action::class);
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('view');
    }

    function it_has_type()
    {
        $this->getType()->shouldReturn('link');
    }

    function it_has_no_label_by_default()
    {
        $this->getLabel()->shouldReturn(null);
    }

    function its_label_is_mutable()
    {
        $this->setLabel('Read book');
        $this->getLabel()->shouldReturn('Read book');
    }

    function it_has_no_icon_by_default()
    {
        $this->getIcon()->shouldReturn(null);
    }

    function its_icon_is_mutable()
    {
        $this->setIcon('checkmark');
        $this->getIcon()->shouldReturn('checkmark');
    }

    function it_has_no_options_by_default()
    {
        $this->getOptions()->shouldReturn([]);
    }

    function it_can_have_options()
    {
        $this->setOptions(['route' => 'sylius_admin_product_update']);
        $this->getOptions()->shouldReturn(['route' => 'sylius_admin_product_update']);
    }

    function it_has_last_position_by_default()
    {
        $this->getPosition()->shouldReturn(100);
    }

    function its_position_is_mutable()
    {
        $this->setPosition(1);
        $this->getPosition()->shouldReturn(1);
    }
}
