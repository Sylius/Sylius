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

use PhpSpec\ObjectBehavior;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionRule;
use Sylius\Component\Promotion\Model\PromotionRuleInterface;

/**
 * @mixin PromotionRule
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
final class PromotionRuleSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(PromotionRule::class);
    }

    function it_should_be_Sylius_promotion_rule()
    {
        $this->shouldImplement(PromotionRuleInterface::class);
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
        $this->setType('type');
        $this->getType()->shouldReturn('type');
    }

    function it_should_initialize_array_for_configuration_by_default()
    {
        $this->getConfiguration()->shouldReturn([]);
    }

    function its_configuration_should_be_mutable()
    {
        $this->setConfiguration(['value' => 500]);
        $this->getConfiguration()->shouldReturn(['value' => 500]);
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
}
