<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\DependencyInjection\Driver\Exception;

use PhpSpec\ObjectBehavior;

/**
 * @author Arnaud Langlade <aRn0D.dev@gmail.com>
 */
class UnknownDriverExceptionSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('driver');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\DependencyInjection\Driver\Exception\UnknownDriverException');
    }

    function it_extends_exception()
    {
        $this->shouldHaveType('\Exception');
    }

    function it_has_a_message()
    {
        $this->getMessage()->shouldReturn('Unknown driver "driver".');
    }
}
