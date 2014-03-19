<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Order\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Order\Model\OrderInterface;

class NumberSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Order\Model\Number');
    }

    function it_implements_Sylius_number_interface()
    {
        $this->shouldImplement('Sylius\Component\Order\Model\NumberInterface');
    }

    function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_does_not_belong_to_an_order_by_default()
    {
        $this->getOrder()->shouldReturn(null);
    }

    function it_allows_assigning_itself_to_an_order(OrderInterface $order)
    {
        $this->setOrder($order);
        $this->getOrder()->shouldReturn($order);
    }
}
