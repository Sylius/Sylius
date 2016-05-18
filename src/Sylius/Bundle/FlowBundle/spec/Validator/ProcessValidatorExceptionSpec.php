<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\FlowBundle\Validator;

use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ProcessValidatorExceptionSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(100);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\FlowBundle\Validator\ProcessValidatorException');
    }

    function it_is_http_exception()
    {
        $this->shouldHaveType(HttpException::class);
    }
}
