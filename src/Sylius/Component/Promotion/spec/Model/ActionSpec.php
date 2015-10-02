<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Promotion\Model;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Promotion\Model\BenefitInterface;
use Sylius\Component\Promotion\Model\FilterInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ActionSpec extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Promotion\Model\Action');
    }

    function it_should_be_Sylius_promotion_action()
    {
        $this->shouldImplement('Sylius\Component\Promotion\Model\ActionInterface');
    }

    function it_should_not_have_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_should_not_have_benefits_by_default()
    {
        $this->getBenefits()->shouldHaveType(ArrayCollection::class);
        $this->getBenefits()->shouldBeEmpty();
    }

    function it_should_not_have_filters_by_default()
    {
        $this->getFilters()->shouldHaveType(ArrayCollection::class);
        $this->getFilters()->shouldBeEmpty();
    }

//    function it_should_not_have_type_by_default()
//    {
//        $this->getType()->shouldReturn(null);
//    }
//
//    function its_type_should_be_mutable()
//    {
//        $this->setType(ActionInterface::TYPE_FIXED_DISCOUNT);
//        $this->getType()->shouldReturn(ActionInterface::TYPE_FIXED_DISCOUNT);
//    }

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

    function its_promotion_by_should_be_mutable(PromotionInterface $promotion)
    {
        $this->setPromotion($promotion);
        $this->getPromotion()->shouldReturn($promotion);
    }

    function its_adding_and_removing_filters(FilterInterface $filter)
    {
        $this->hasFilter($filter)->shouldReturn(false);

        $filter->setAction($this)->shouldBeCalled();
        $this->addFilter($filter);

        $this->hasFilter($filter)->shouldReturn(true);

        $filter->unsetAction()->shouldBeCalled();
        $this->removeFilter($filter)->shouldReturn(null);
        $this->hasFilter($filter)->shouldReturn(false);
    }

    function its_adding_and_removing_benefits(BenefitInterface $benefit)
    {
        $this->hasBenefit($benefit)->shouldReturn(false);

        $benefit->setAction($this)->shouldBeCalled();
        $this->addBenefit($benefit);

        $this->hasBenefit($benefit)->shouldReturn(true);

        $benefit->unsetAction()->shouldBeCalled();
        $this->removeBenefit($benefit)->shouldReturn(null);
        $this->hasBenefit($benefit)->shouldReturn(false);
    }

}
