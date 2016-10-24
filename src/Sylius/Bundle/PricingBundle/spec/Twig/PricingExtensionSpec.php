<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\PricingBundle\Twig;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\PricingBundle\Templating\Helper\PricingHelper;
use Sylius\Bundle\PricingBundle\Twig\PricingExtension;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class PricingExtensionSpec extends ObjectBehavior
{
    function let(PricingHelper $helper)
    {
        $this->beConstructedWith($helper);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PricingExtension::class);
    }

    function it_is_a_Twig_extension()
    {
        $this->shouldHaveType('Twig_Extension');
    }
}
