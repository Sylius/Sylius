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
use Sylius\Component\Order\Model\CommentInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

/**
 * @author Myke Hines <myke@webhines.com>
 */
class CommentSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Order\Model\Comment');
    }

    function it_implements_Sylius_comment_interface()
    {
        $this->shouldImplement(CommentInterface::class);
    }

    function it_implements_Sylius_timestampable_interface()
    {
        $this->shouldImplement(TimestampableInterface::class);
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
