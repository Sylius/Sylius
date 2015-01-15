<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\InstallerBundle\Requirement;

use PhpSpec\ObjectBehavior;

class RequirementSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('PHP version', true, '5.4', '5.5', true, 'Please upgrade.');
    }

    function it_gets_label()
    {
        $this->getLabel()->shouldReturn('PHP version');
    }

    function it_gets_fulfilled()
    {
        $this->isFulfilled()->shouldReturn(true);
    }

    function it_gets_expected()
    {
        $this->getExpected()->shouldReturn('5.4');
    }

    function it_gets_actual()
    {
        $this->getActual()->shouldReturn('5.5');
    }

    function it_gets_required()
    {
        $this->isRequired()->shouldReturn(true);
    }

    function it_gets_help()
    {
        $this->getHelp()->shouldReturn('Please upgrade.');
    }
}
