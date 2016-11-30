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

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Promotion\Model\PromotionActionInterface;
use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Sylius\Component\Promotion\Model\Promotion;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionRuleInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
final class PromotionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Promotion::class);
    }

    function it_is_a_promotion()
    {
        $this->shouldImplement(PromotionInterface::class);
    }

    function it_does_not_have_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_mutable_code()
    {
        $this->setCode('P1');
        $this->getCode()->shouldReturn('P1');
    }

    function its_name_is_mutable()
    {
        $this->setName('New Year Sale');
        $this->getName()->shouldReturn('New Year Sale');
    }

    function its_description_is_mutable()
    {
        $this->setDescription('New Year Sale 50% off.');
        $this->getDescription()->shouldReturn('New Year Sale 50% off.');
    }

    function its_priority_is_mutable()
    {
        $this->setPriority(5);
        $this->getPriority()->shouldReturn(5);
    }

    function its_not_exclusive_by_default()
    {
        $this->isExclusive()->shouldReturn(false);
    }

    function its_exclusive_is_mutable()
    {
        $this->setExclusive(true);
        $this->isExclusive()->shouldReturn(true);
    }

    function it_does_not_have_usage_limit_by_default()
    {
        $this->getUsageLimit()->shouldReturn(null);
    }

    function its_usage_limit_is_mutable()
    {
        $this->setUsageLimit(10);
        $this->getUsageLimit()->shouldReturn(10);
    }

    function it_does_not_have_used_by_default()
    {
        $this->getUsed()->shouldReturn(0);
    }

    function its_used_is_mutable()
    {
        $this->setUsed(5);
        $this->getUsed()->shouldReturn(5);
    }

    function its_increments_and_decrements_its_used_value()
    {
        $this->incrementUsed();
        $this->incrementUsed();
        $this->getUsed()->shouldReturn(2);

        $this->decrementUsed();
        $this->getUsed()->shouldReturn(1);
    }

    function its_starts_at_is_mutable(\DateTime $date)
    {
        $this->setStartsAt($date);
        $this->getStartsAt()->shouldReturn($date);
    }

    function its_ends_at_is_mutable(\DateTime $date)
    {
        $this->setEndsAt($date);
        $this->getEndsAt()->shouldReturn($date);
    }

    function it_initializes_coupons_collection_by_default()
    {
        $this->getCoupons()->shouldHaveType(Collection::class);
    }

    function it_adds_coupons_properly(PromotionCouponInterface $coupon)
    {
        $this->hasCoupon($coupon)->shouldReturn(false);

        $coupon->setPromotion($this)->shouldBeCalled();
        $this->addCoupon($coupon);

        $this->hasCoupon($coupon)->shouldReturn(true);
    }

    function it_removes_coupons_properly(PromotionCouponInterface $coupon)
    {
        $this->hasCoupon($coupon)->shouldReturn(false);

        $coupon->setPromotion($this)->shouldBeCalled();
        $this->addCoupon($coupon);

        $coupon->setPromotion(null)->shouldBeCalled();
        $this->removeCoupon($coupon);

        $this->hasCoupon($coupon)->shouldReturn(false);
    }

    function it_initializes_rules_collection_by_default()
    {
        $this->getRules()->shouldHaveType(Collection::class);
    }

    function it_adds_rules_properly(PromotionRuleInterface $rule)
    {
        $this->hasRule($rule)->shouldReturn(false);

        $rule->setPromotion($this)->shouldBeCalled();
        $this->addRule($rule);

        $this->hasRule($rule)->shouldReturn(true);
    }

    function it_removes_rules_properly(PromotionRuleInterface $rule)
    {
        $this->hasRule($rule)->shouldReturn(false);

        $rule->setPromotion($this)->shouldBeCalled();
        $this->addRule($rule);

        $rule->setPromotion(null)->shouldBeCalled();
        $this->removeRule($rule);

        $this->hasRule($rule)->shouldReturn(false);
    }

    function it_initializes_actions_collection_by_default()
    {
        $this->getActions()->shouldHaveType(Collection::class);
    }

    function it_adds_actions_properly(PromotionActionInterface $action)
    {
        $this->hasAction($action)->shouldReturn(false);

        $action->setPromotion($this)->shouldBeCalled();
        $this->addAction($action);

        $this->hasAction($action)->shouldReturn(true);
    }

    function it_removes_actions_properly(PromotionActionInterface $action)
    {
        $this->hasAction($action)->shouldReturn(false);

        $action->setPromotion($this)->shouldBeCalled();
        $this->addAction($action);

        $action->setPromotion(null)->shouldBeCalled();
        $this->removeAction($action);

        $this->hasAction($action)->shouldReturn(false);
    }
}
