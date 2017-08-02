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

namespace spec\Sylius\Bundle\ResourceBundle\DependencyInjection\Driver\Exception;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Driver\Exception\InvalidDriverException;

/**
 * @author Arnaud Langlade <aRn0D.dev@gmail.com>
 */
final class InvalidDriverExceptionSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('driver', 'className');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(InvalidDriverException::class);
    }

    function it_extends_exception()
    {
        $this->shouldHaveType(\Exception::class);
    }

    function it_has_a_message()
    {
        $this->getMessage()->shouldReturn('Driver "driver" is not supported by className.');
    }
}
