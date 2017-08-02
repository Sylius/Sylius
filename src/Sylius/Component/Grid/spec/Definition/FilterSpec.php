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
use Sylius\Component\Grid\Definition\Filter;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class FilterSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedThrough('fromNameAndType', ['keywords', 'string']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Filter::class);
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('keywords');
    }

    function it_has_type()
    {
        $this->getType()->shouldReturn('string');
    }

    function it_has_label_which_defaults_to_name()
    {
        $this->getLabel()->shouldReturn('keywords');

        $this->setLabel('Search by keyword');
        $this->getLabel()->shouldReturn('Search by keyword');
    }

    function it_has_no_template_by_default()
    {
        $this->getTemplate()->shouldReturn(null);
    }

    function its_template_is_mutable()
    {
        $this->setTemplate('SyliusGridBundle:Filter:template.html.twig');
        $this->getTemplate()->shouldReturn('SyliusGridBundle:Filter:template.html.twig');
    }

    function it_has_no_options_by_default()
    {
        $this->getOptions()->shouldReturn([]);
    }

    function it_can_have_options()
    {
        $this->setOptions(['fields' => ['firstName', 'lastName', 'email']]);
        $this->getOptions()->shouldReturn(['fields' => ['firstName', 'lastName', 'email']]);
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

    function it_has_no_criteria_by_default()
    {
        $this->getCriteria()->shouldReturn(null);
    }

    function its_criteria_is_mutable()
    {
        $this->setCriteria('false');
        $this->getCriteria()->shouldReturn('false');

        $this->setCriteria(['type' => 'contains']);
        $this->getCriteria()->shouldReturn(['type' => 'contains']);
    }
}
