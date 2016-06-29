<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Cart\Resolver;

use PhpSpec\ObjectBehavior;

/**
 * @author Julien Janvier <j.janvier@gmail.com>
 */
class ItemResolvingExceptionSpec  extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Cart\Resolver\ItemResolvingException');
    }

    function it_is_an_invalid_argument_exception()
    {
        $this->shouldHaveType(\InvalidArgumentException::class);
    }
}
