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

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class FieldSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedThrough('fromNameAndType', ['enabled', 'boolean']);
    }

    function it_has_name(): void
    {
        $this->getName()->shouldReturn('enabled');
    }

    function it_has_type(): void
    {
        $this->getType()->shouldReturn('boolean');
    }

    function it_has_path_which_defaults_to_name(): void
    {
        $this->getPath()->shouldReturn('enabled');

        $this->setPath('method.enabled');
        $this->getPath()->shouldReturn('method.enabled');
    }

    function it_has_label_which_defaults_to_name(): void
    {
        $this->getLabel()->shouldReturn('enabled');

        $this->setLabel('Is enabled?');
        $this->getLabel()->shouldReturn('Is enabled?');
    }

    function it_is_toggleable(): void
    {
        $this->isEnabled()->shouldReturn(true);

        $this->setEnabled(false);
        $this->isEnabled()->shouldReturn(false);
        $this->setEnabled(true);
        $this->isEnabled()->shouldReturn(true);
    }

    function it_knows_by_which_property_it_can_be_sorted(): void
    {
        $this->getSortable()->shouldReturn(null);

        $this->setSortable('method.enabled');
        $this->getSortable()->shouldReturn('method.enabled');
    }

    function its_sorted_by_name_when_sortable_is_not_set(): void
    {
        $this->getSortable()->shouldReturn(null);

        $this->setSortable('enabled');
        $this->getSortable()->shouldReturn('enabled');
    }

    function it_has_no_options_by_default(): void
    {
        $this->getOptions()->shouldReturn([]);
    }

    function it_can_have_options(): void
    {
        $this->setOptions(['template' => 'SyliusUiBundle:Grid/Field:_status.html.twig']);
        $this->getOptions()->shouldReturn(['template' => 'SyliusUiBundle:Grid/Field:_status.html.twig']);
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
