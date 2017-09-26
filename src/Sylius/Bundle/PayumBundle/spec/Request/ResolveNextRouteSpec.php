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

namespace spec\Sylius\Bundle\PayumBundle\Request;

use Payum\Core\Security\TokenInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\PayumBundle\Request\ResolveNextRoute;
use Sylius\Bundle\PayumBundle\Request\ResolveNextRouteInterface;

final class ResolveNextRouteSpec extends ObjectBehavior
{
    function let(TokenInterface $token): void
    {
        $this->beConstructedWith($token);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(ResolveNextRoute::class);
    }

    function it_is_resolve_next_route_request(): void
    {
        $this->shouldImplement(ResolveNextRouteInterface::class);
    }

    function it_has_next_route_name(): void
    {
        $this->setRouteName('route_name');

        $this->getRouteName()->shouldReturn('route_name');
    }

    function it_has_next_route_parameters(): void
    {
        $this->setRouteParameters(['id' => 1]);

        $this->getRouteParameters()->shouldReturn(['id' => 1]);
    }

    function it_does_not_have_route_name_by_default(): void
    {
        $this->getRouteName()->shouldReturn(null);
    }

    function it_does_not_have_route_parameters_by_default(): void
    {
        $this->getRouteParameters()->shouldReturn([]);
    }
}
