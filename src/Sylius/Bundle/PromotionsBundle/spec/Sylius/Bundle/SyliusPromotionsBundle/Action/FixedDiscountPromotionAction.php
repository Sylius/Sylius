<?php

namespace spec\Sylius\Bundle\PromotionsBundle\Action;

use PHPSpec2\ObjectBehavior;

/**
 * Promotion action spec.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class FixedDiscountPromotionAction extends ObjectBehavior
{
    /**
     * @param Sylius\Bundle\ResourceBundle\Model\RepositoryInterface $repository
     */
    function let($repository)
    {
        $this->beConstructedWith($repository);
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PromotionsBundle\Action\FixedDiscountPromotionAction');
    }

    function it_should_be_Sylius_promotion_action()
    {
        $this->shouldImplement('Sylius\Bundle\PromotionsBundle\Action\PromotionActionInterface');
    }

    /**
     * @param Sylius\Bundle\SalesBundle\Model\OrderInterface      $order
     * @param Sylius\Bundle\SalesBundle\Model\AdjustmentInterface $adjustment
     */
    function it_should_add_adjustment_to_order($repository, $order, $adjustment)
    {
        $amount = 10;

        $repository->createNew()->shouldBeCalled()->willReturn($adjustment);
        $adjustment->setAmount($amount)->shouldBeCalled();
        $order->addAdjustment($adjustment)->shouldBeCalled();

        $this->execute($order, array('amount' => $amount));
    }

    function it_should_return_fixed_discount_configuration_form_type()
    {
        $this->getConfigurationFormType()->shouldReturn('sylius_promotion_action_fixed_discount_configuration');
    }
}
