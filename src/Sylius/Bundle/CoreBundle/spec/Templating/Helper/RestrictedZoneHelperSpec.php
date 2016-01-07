<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Templating\Helper;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Addressing\Checker\RestrictedZoneCheckerInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\Templating\Helper\Helper;

class RestrictedZoneHelperSpec extends ObjectBehavior
{
    function let(RestrictedZoneCheckerInterface $restrictedZoneChecker)
    {
        $this->beConstructedWith($restrictedZoneChecker);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Templating\Helper\RestrictedZoneHelper');
    }

    function it_is_a_twig_extension()
    {
        $this->shouldHaveType(Helper::class);
    }

    function it_uses_restricted_zone_checker(
        $restrictedZoneChecker,
        ProductInterface $product
    ) {
        $restrictedZoneChecker->isRestricted($product)->shouldBeCalled($product)->willReturn(false);
        $this->isRestricted($product)->shouldReturn(false);

        $restrictedZoneChecker->isRestricted($product)->shouldBeCalled($product)->willReturn(true);
        $this->isRestricted($product)->shouldReturn(true);
    }
}
