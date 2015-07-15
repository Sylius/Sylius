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

class RestrictedZoneHelperSpec extends ObjectBehavior
{
    public function let(RestrictedZoneCheckerInterface $restrictedZoneChecker)
    {
        $this->beConstructedWith($restrictedZoneChecker);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Templating\Helper\RestrictedZoneHelper');
    }

    public function it_is_a_twig_extension()
    {
        $this->shouldHaveType('Symfony\Component\Templating\Helper\Helper');
    }

    public function it_uses_restricted_zone_checker(
        $restrictedZoneChecker,
        ProductInterface $product
    ) {
        $restrictedZoneChecker->isRestricted($product)->shouldBeCalled($product)->willReturn(false);
        $this->isRestricted($product)->shouldReturn(false);

        $restrictedZoneChecker->isRestricted($product)->shouldBeCalled($product)->willReturn(true);
        $this->isRestricted($product)->shouldReturn(true);
    }
}
