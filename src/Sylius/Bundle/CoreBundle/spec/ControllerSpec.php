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

namespace spec\Sylius\Bundle\CoreBundle;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\CommandBus;
use Sylius\Bundle\CoreBundle\Request;

final class ControllerSpec extends ObjectBehavior
{
    function let(CommandBus $commandBus)
    {
        $this->beConstructedWith($commandBus);
    }

    function it_uses_command_bus_to_handle_command(CommandBus $commandBus, Request $request)
    {
        $request->getUrl(42)->willReturn('url');
        $request->getUrl(24)->willReturn('url2');

        $commandBus->handle(Argument::cetera())->shouldBeCalled();

        $this->fooAction($request);
    }
}
