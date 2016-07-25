<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Resource\Repository\Exception;

use PhpSpec\ObjectBehavior;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class ExistingResourceExceptionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Resource\Repository\Exception\ExistingResourceException');
    }

    function it_extends_exception()
    {
        $this->shouldHaveType(\Exception::class);
    }

    function it_has_a_message()
    {
        $this->getMessage()->shouldReturn('Given resource already exists in the repository.');
    }
}
