<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Promotion\Action;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Model\OrderInterface;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class FixedDiscountActionSpec extends ObjectBehavior
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
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Promotion\Action\FixedDiscountAction');
    }

    function it_implements_Sylius_promotion_action_interface()
    {
        $this->shouldImplement('Sylius\Component\Promotion\Action\PromotionActionInterface');
    }

    /**
     * @param Sylius\Bundle\CoreBundle\Model\OrderInterface       $order
     * @param Sylius\Bundle\OrderBundle\Model\AdjustmentInterface $adjustment
     */
    function it_applies_fixed_discount_as_promotion_adjustment($adjustmentRepository, $order, $adjustment)
    {
        $adjustmentRepository->createNew()->willReturn($adjustment);

        $adjustment->setAmount(-500)->shouldBeCalled();
        $adjustment->setLabel(OrderInterface::PROMOTION_ADJUSTMENT)->shouldBeCalled();

        $order->addAdjustment($adjustment)->shouldBeCalled();
        $configuration = array('amount' => 500);

        $this->execute($order, $configuration);
    }
}
