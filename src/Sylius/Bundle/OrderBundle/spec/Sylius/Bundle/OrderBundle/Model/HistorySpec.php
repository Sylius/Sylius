<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\OrderBundle\Model;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\OrderBundle\Model\AdjustmentInterface;
use Sylius\Bundle\OrderBundle\Model\OrderItemInterface;

/**
 * @author Myke Hines <myke@webhines.com>
 */
class HistorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\OrderBundle\Model\History');
    }

    function it_implements_Sylius_history_interface()
    {
        $this->shouldImplement('Sylius\Bundle\OrderBundle\Model\HistoryInterface');
    }

    function it_implements_Sylius_timestampable_interface()
    {
        $this->shouldImplement('Sylius\Bundle\ResourceBundle\Model\TimestampableInterface');
    }

    function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_no_order_by_default()
    {
        $this->getOrder()->shouldReturn(null);
    }

    function it_has_no_state_by_default()
    {
        $this->getState()->shouldReturn(null);
    }

    function its_comment_is_mutable()
    {
        $this->setComment('001351');
        $this->getComment()->shouldReturn('001351');
    }

}
