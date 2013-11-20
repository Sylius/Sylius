<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\SyliusN\Bundle\CoreBundle\Promotion\Action;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Model\OrderInterface;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class PercentageDiscountActionSpec extends ObjectBehavior
{
    /**
     * @param Sylius\Bundle\ResourceBundle\Model\RepositoryInterface $adjustmentRepository
     */
    function let($adjustmentRepository)
    {
        $this->beConstructedWith($adjustmentRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Promotion\Action\PercentageDiscountAction');
    }

    function it_implements_Sylius_promotion_action_interface()
    {
        $this->shouldImplement('Sylius\Bundle\PromotionsBundle\Action\PromotionActionInterface');
    }

    /**
     * @param Sylius\Bundle\CoreBundle\Model\OrderInterface           $order
     * @param Sylius\Bundle\OrderBundle\Model\AdjustmentInterface     $adjustment
     * @param Sylius\Bundle\PromotionsBundle\Model\PromotionInterface $promotion
     */
    function it_applies_percentage_discount_as_promotion_adjustment($adjustmentRepository, $order, $adjustment, $promotion)
    {
        $order->getPromotionSubjectItemTotal()->willReturn(10000);
        $adjustmentRepository->createNew()->willReturn($adjustment);
        $promotion->getDescription()->willReturn('promotion description');

        $adjustment->setAmount(-2500)->shouldBeCalled();
        $adjustment->setLabel(OrderInterface::PROMOTION_ADJUSTMENT)->shouldBeCalled();
        $adjustment->setDescription('promotion description')->shouldBeCalled();
        $order->addAdjustment($adjustment)->shouldBeCalled();

        $configuration = array('percentage' => 0.25);

        $this->execute($order, $configuration);
    }
}
