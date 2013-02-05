<?php

namespace spec\Sylius\Bundle\PromotionsBundle\Model;

use PHPSpec2\ObjectBehavior;
use Sylius\Bundle\PromotionsBundle\Model\ActionInterface;

/**
 * Promotion action model spec.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class Action extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PromotionsBundle\Model\Action');
    }

    function it_should_be_Sylius_promotion_action()
    {
        $this->shouldImplement('Sylius\Bundle\PromotionsBundle\Model\ActionInterface');
    }

    function it_should_not_have_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_should_not_have_type_by_default()
    {
        $this->getType()->shouldReturn(null);
    }

    function its_type_should_be_mutable()
    {
        $this->setType(ActionInterface::TYPE_FIXED_DISCOUNT);
        $this->getType()->shouldReturn(ActionInterface::TYPE_FIXED_DISCOUNT);
    }

    function it_should_initialize_array_for_configuration_by_default()
    {
        $this->getConfiguration()->shouldReturn(array());
    }

    function its_configuration_should_be_mutable()
    {
        $this->setConfiguration(array('value' => 500));
        $this->getConfiguration()->shouldReturn(array('value' => 500));
    }

    function it_should_not_have_promotion_by_default()
    {
        $this->getPromotion()->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\PromotionsBundle\Model\PromotionInterface $promotion
     */
    function its_promotion_by_should_be_mutable($promotion)
    {
        $this->setPromotion($promotion);
        $this->getPromotion()->shouldReturn($promotion);
    }
}
