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
class UnknownDriverExceptionSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith('driver');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Resource\Exception\Driver\UnknownDriverException');
    }

    public function it_should_extends_exception()
    {
        $this->shouldHaveType('\Exception');
    }
}
