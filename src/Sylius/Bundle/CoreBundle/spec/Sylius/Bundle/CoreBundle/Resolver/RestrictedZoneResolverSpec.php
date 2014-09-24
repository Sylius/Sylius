<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Resolver;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Addressing\Checker\RestrictedZoneCheckerInterface;
use Sylius\Component\Core\Model\OrderItemInterface;

class RestrictedZoneSpec extends ObjectBehavior
{
    function let(RestrictedZoneCheckerInterface $restrictedZoneChecker)
    {
        $this->beConstructedWith($restrictedZoneChecker);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Resolver\RestrictedZoneResolver');
    }

    function it_implements_item_resolver_interface()
    {
        $this->shouldImplement('Sylius\Component\Cart\Resolver\ItemResolverInterface');
    }

    function it_should_check_zone_is_restricted(
        $restrictedZoneChecker,
        OrderItemInterface $item
    ) {
        $restrictedZoneChecker->isRestricted(array())->willReturn(false);

        $this->resolve($item, array());
    }

    function it_throws_exception_if_zone_is_restricted(
        $restrictedZoneChecker,
        OrderItemInterface $item
    ) {
        $restrictedZoneChecker->isRestricted(array())->willReturn(true);

        $this
            ->shouldThrow('Sylius\Component\Cart\Resolver\ItemResolvingException')
            ->duringResolve($item, array())
        ;
    }
}
