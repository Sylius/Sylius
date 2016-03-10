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
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Symfony\Component\Templating\Helper\Helper;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class RegularPriceHelperSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Templating\Helper\RegularPriceHelper');
    }

    function it_is_helper()
    {
        $this->shouldHaveType(Helper::class);
    }

    function it_returns_regular_price_of_discount_order_item(OrderItemInterface $orderItem)
    {
        $orderItem->getQuantity()->willReturn(2);
        $orderItem->getUnitPrice()->willReturn(1000);

        $orderItem->getAdjustmentsTotalRecursively(AdjustmentInterface::TAX_ADJUSTMENT)->willReturn(1000);

        $this->getRegularPrice($orderItem)->shouldReturn(3000);
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_regular_price');
    }
}
