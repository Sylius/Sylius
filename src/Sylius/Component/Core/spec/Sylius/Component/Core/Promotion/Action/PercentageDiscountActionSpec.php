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
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Model\AdjustmentInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class PercentageDiscountActionSpec extends ObjectBehavior
{
    function let(RepositoryInterface $adjustmentRepository)
    {
        $this->beConstructedWith($adjustmentRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Promotion\Action\PercentageDiscountAction');
    }

    function it_implements_Sylius_promotion_action_interface()
    {
        $this->shouldImplement('Sylius\Component\Promotion\Action\PromotionActionInterface');
    }

    function it_applies_percentage_discount_as_promotion_adjustment(
        $adjustmentRepository,
        OrderInterface $order,
        AdjustmentInterface $adjustment,
        PromotionInterface $promotion
    )
    {
        $order->getPromotionSubjectItemTotal()->willReturn(10000);
        $adjustmentRepository->createNew()->willReturn($adjustment);
        $promotion->getDescription()->willReturn('promotion description');

        $adjustment->setAmount(-2500)->shouldBeCalled();
        $adjustment->setLabel(OrderInterface::PROMOTION_ADJUSTMENT)->shouldBeCalled();
        $adjustment->setDescription('promotion description')->shouldBeCalled();
        $order->addAdjustment($adjustment)->shouldBeCalled();

        $configuration = array('percentage' => 0.25);

        $this->execute($order, $configuration, $promotion);
    }
}
