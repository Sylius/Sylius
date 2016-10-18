<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\PayumBundle\Action\Offline;

use Payum\Core\Action\ActionInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\PayumBundle\Action\Offline\ResolveNextRouteAction;
use Sylius\Bundle\PayumBundle\Request\ResolveNextRoute;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ResolveNextRouteActionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ResolveNextRouteAction::class);
    }

    function it_is_a_payum_action()
    {
        $this->shouldImplement(ActionInterface::class);
    }

    function it_resolves_next_route(ResolveNextRoute $resolveNextRouteRequest)
    {
        $resolveNextRouteRequest->setRouteName('sylius_shop_order_thank_you')->shouldBeCalled();

        $this->execute($resolveNextRouteRequest);
    }
}
