<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Promotion\Action;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Event\AdjustmentEvent;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Originator\Originator\OriginatorInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @mixin \Sylius\Component\Core\Promotion\Action\FixedDiscountAction
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class FixedDiscountActionSpec extends ObjectBehavior
{
    function let(
        FactoryInterface $adjustmentFactory,
        OriginatorInterface $originator,
        EventDispatcherInterface $eventDispatcher
    )
    {
        $this->beConstructedWith($adjustmentFactory, $originator, $eventDispatcher);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Promotion\Action\FixedDiscountAction');
    }

    function it_implements_Sylius_promotion_action_interface()
    {
        $this->shouldImplement('Sylius\Component\Promotion\Action\PromotionActionInterface');
    }

    function it_applies_fixed_discount_as_promotion_adjustment(
        OrderInterface $order,
        PromotionInterface $promotion,
        EventDispatcherInterface $eventDispatcher
    ) {
        $promotion->getDescription()->shouldBeCalled()->willReturn('promotion description');
        $promotion->getId()->shouldBeCalled()->willReturn(123);

        $configuration = array('amount' => 500);

        $eventDispatcher->dispatch(AdjustmentEvent::ADJUSTMENT_ADDING_ORDER,
            Argument::type(AdjustmentEvent::class)
        )->shouldBeCalled();

        $this->execute($order, $configuration, $promotion);
    }
}
