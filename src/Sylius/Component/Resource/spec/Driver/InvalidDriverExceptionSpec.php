<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Resource\Exception\Driver;

use PhpSpec\ObjectBehavior;

/**
 * @author Arnaud Langlade <aRn0D.dev@gmail.com>
 */
class InvalidDriverExceptionSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('driver', 'className');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Resource\Exception\Driver\InvalidDriverException');
    }

    function it_should_extends_exception()
    {
        $this->shouldHaveType('\Exception');
    }
}
