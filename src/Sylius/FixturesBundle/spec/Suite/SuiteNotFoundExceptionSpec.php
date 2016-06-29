<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\FixturesBundle\Suite;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\FixturesBundle\Suite\SuiteNotFoundException;

/**
 * @mixin SuiteNotFoundException
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class SuiteNotFoundExceptionSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('suite_name');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\FixturesBundle\Suite\SuiteNotFoundException');
    }

    function it_is_an_invalid_argument_exception()
    {
        $this->shouldHaveType(\InvalidArgumentException::class);
    }

    function it_has_preformatted_message()
    {
        $this->getMessage()->shouldReturn('Suite with name "suite_name" could not be found!');
    }
}
