<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionsBundle\Model;

use Sylius\Bundle\ResourceBundle\Model\TimestampableInterface;

/**
 * Promotion model interface.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface PromotionInterface extends TimestampableInterface
{
    public function getName();
    public function setName($name);
    public function getDescription();
    public function setDescription($description);
    public function getUsageLimit();
    public function setUsageLimit($usageLimit);
    public function getUsed();
    public function setUsed($used);
    public function incrementUsed();
    public function getStartsAt();
    public function setStartsAt(\DateTime $startsAt = null);
    public function getEndsAt();
    public function setEndsAt(\DateTime $endsAt = null);

    /**
     * @return Boolean
     */
    public function isCouponBased();

    /**
     * @param Boolean $couponBased
     *
     * @return self
     */
    public function setCouponBased($couponBased);

    /**
     * @return CouponInterface[]
     */
    public function getCoupons();

    /**
     * @param CouponInterface $coupon
     *
     * @return Boolean
     */
    public function hasCoupon(CouponInterface $coupon);

    /**
     * @return Boolean
     */
    public function hasCoupons();

    /**
     * @param CouponInterface $coupon
     *
     * @return self
     */
    public function addCoupon(CouponInterface $coupon);

    /**
     * @param CouponInterface $coupon
     *
     * @return self
     */
    public function removeCoupon(CouponInterface $coupon);

    /**
     * @return RuleInterface[]
     */
    public function getRules();

    /**
     * @param RuleInterface $rule
     *
     * @return Boolean
     */
    public function hasRule(RuleInterface $rule);

    /**
     * @return Boolean
     */
    public function hasRules();

    /**
     * @param RuleInterface $rule
     *
     * @return self
     */
    public function addRule(RuleInterface $rule);

    /**
     * @param RuleInterface $rule
     *
     * @return self
     */
    public function removeRule(RuleInterface $rule);

    /**
     * @return ActionInterface[]
     */
    public function getActions();

    /**
     * @param ActionInterface $action
     *
     * @return Boolean
     */
    public function hasAction(ActionInterface $action);

    /**
     * @return Boolean
     */
    public function hasActions();

    /**
     * @param ActionInterface $action
     *
     * @return self
     */
    public function addAction(ActionInterface $action);

    /**
     * @param ActionInterface $action
     *
     * @return self
     */
    public function removeAction(ActionInterface $action);
}
