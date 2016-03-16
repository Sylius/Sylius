<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Loader;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ThemeBundle\Loader\ThemeLoadingFailedException;

/**
 * @mixin ThemeLoadingFailedException
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ThemeLoadingFailedExceptionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ThemeBundle\Loader\ThemeLoadingFailedException');
    }

    function it_is_a_domain_exception()
    {
        $this->shouldHaveType(\DomainException::class);
    }

    function it_is_a_logic_exception()
    {
        $this->shouldHaveType(\LogicException::class);
    }
}
