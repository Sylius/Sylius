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
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Originator\Originator\OriginatorInterface;
use Sylius\Component\Promotion\Action\PromotionActionInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class PercentageDiscountActionSpec extends ObjectBehavior
{
    function let(FactoryInterface $adjustmentFactory, OriginatorInterface $originator)
    {
        $this->beConstructedWith($adjustmentFactory, $originator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Promotion\Action\PercentageDiscountAction');
    }

    function it_implements_Sylius_promotion_action_interface()
    {
        $this->shouldImplement(PromotionActionInterface::class);
    }

    function it_applies_percentage_discount_as_promotion_adjustment(
        FactoryInterface $adjustmentFactory,
        $originator,
        OrderInterface $order,
        AdjustmentInterface $adjustment,
        PromotionInterface $promotion
    ) {
        $order->getPromotionSubjectTotal()->willReturn(10000);
        $adjustmentFactory->createNew()->willReturn($adjustment);
        $promotion->getDescription()->willReturn('promotion description');

        $adjustment->setAmount(-2500)->shouldBeCalled();
        $adjustment->setType(AdjustmentInterface::PROMOTION_ADJUSTMENT)->shouldBeCalled();
        $adjustment->setLabel('promotion description')->shouldBeCalled();

        $originator->setOrigin($adjustment, $promotion)->shouldBeCalled();

        $order->addAdjustment($adjustment)->shouldBeCalled();

        $configuration = ['percentage' => 0.25];

        $this->execute($order, $configuration, $promotion);
    }
}
