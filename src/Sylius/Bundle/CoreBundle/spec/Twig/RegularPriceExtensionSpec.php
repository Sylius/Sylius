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
use Sylius\Bundle\CoreBundle\Templating\Helper\RegularPriceHelper;
use Sylius\Component\Core\Model\OrderItemInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class RegularPriceExtensionSpec extends ObjectBehavior
{
    function let(RegularPriceHelper $regularPriceHelper)
    {
        $this->beConstructedWith($regularPriceHelper);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Twig\RegularPriceExtension');
    }

    function it_is_twig_extension()
    {
        $this->shouldHaveType(\Twig_Extension::class);
    }

    function it_returns_order_item_regular_price($regularPriceHelper, OrderItemInterface $orderItem)
    {
        $regularPriceHelper->getRegularPrice($orderItem)->willReturn(1500);

        $this->getItemRegularPrice($orderItem)->shouldReturn(1500);
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_regular_price');
    }
}
