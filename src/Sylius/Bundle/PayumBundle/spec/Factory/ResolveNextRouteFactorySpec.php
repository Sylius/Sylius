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

namespace spec\Sylius\Bundle\PayumBundle\Factory;

use Payum\Core\Security\TokenInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\PayumBundle\Factory\ResolveNextRouteFactory;
use Sylius\Bundle\PayumBundle\Factory\ResolveNextRouteFactoryInterface;
use Sylius\Bundle\PayumBundle\Request\ResolveNextRoute;

final class ResolveNextRouteFactorySpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(ResolveNextRouteFactory::class);
    }

    function it_is_resolve_next_route_factory(): void
    {
        $this->shouldImplement(ResolveNextRouteFactoryInterface::class);
    }

    function it_creates_resolve_next_route_request(TokenInterface $token): void
    {
        $this->createNewWithModel($token)->shouldBeLike(new ResolveNextRoute($token->getWrappedObject()));
    }
}
