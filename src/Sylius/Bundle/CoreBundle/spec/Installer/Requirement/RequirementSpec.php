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

namespace spec\Sylius\Bundle\CoreBundle\Installer\Requirement;

use PhpSpec\ObjectBehavior;

final class RequirementSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith('PHP version', true, true, 'Please upgrade.');
    }

    function it_gets_label(): void
    {
        $this->getLabel()->shouldReturn('PHP version');
    }

    function it_gets_fulfilled(): void
    {
        $this->isFulfilled()->shouldReturn(true);
    }

    function it_gets_required(): void
    {
        $this->isRequired()->shouldReturn(true);
    }

    function it_gets_help(): void
    {
        $this->getHelp()->shouldReturn('Please upgrade.');
    }
}
