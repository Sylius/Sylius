<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Twig;

use PhpSpec\ObjectBehavior;

class SyliusRestrictedZoneExtensionSpec extends ObjectBehavior
{
    /**
     * @param Sylius\Bundle\CoreBundle\Checker\RestrictedZoneCheckerInterface $restrictedZoneChecker
     */
    function let($restrictedZoneChecker)
    {
        $this->beConstructedWith($restrictedZoneChecker);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Twig\SyliusRestrictedZoneExtension');
    }

    function it_is_a_twig_extension()
    {
        $this->shouldHaveType('Twig_Extension');
    }

    /**
     * @param Sylius\Bundle\CoreBundle\Model\ProductInterface $product
     */
    function it_uses_restricted_zone_checker($restrictedZoneChecker, $product)
    {
        $restrictedZoneChecker->isRestricted($product)->shouldBeCalled($product)->willReturn(false);
        $this->isRestricted($product)->shouldReturn(false);

        $restrictedZoneChecker->isRestricted($product)->shouldBeCalled($product)->willReturn(true);
        $this->isRestricted($product)->shouldReturn(true);
    }
}
