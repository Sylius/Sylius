<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Promotion\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface PromotionInterface extends CodeAwareInterface, TimestampableInterface, ResourceInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $description
     */
    public function setDescription($description);

    /**
     * @return int
     */
    public function getPriority();

    /**
     * @param int $priority
     */
    public function setPriority($priority);

    /**
     * @return bool
     */
    public function isExclusive();

    /**
     * @param bool $exclusive
     */
    public function setExclusive($exclusive);

    /**
     * @return int
     */
    public function getUsageLimit();

    /**
     * @param int $usageLimit
     */
    public function setUsageLimit($usageLimit);

    /**
     * @return int
     */
    public function getUsed();

    /**
     * @param int $used
     */
    public function setUsed($used);

    public function incrementUsed();

    public function decrementUsed();

    /**
     * @return \DateTime
     */
    public function getStartsAt();

    /**
     * @param \DateTime $startsAt
     */
    public function setStartsAt(\DateTime $startsAt = null);

    /**
     * @return \DateTime
     */
    public function getEndsAt();

    /**
     * @param \DateTime $endsAt
     */
    public function setEndsAt(\DateTime $endsAt = null);

    /**
     * @return bool
     */
    public function isCouponBased();

    /**
     * @param bool $couponBased
     */
    public function setCouponBased($couponBased);

    /**
     * @return Collection|PromotionCouponInterface[]
     */
    public function getCoupons();

    /**
     * @param PromotionCouponInterface $coupon
     *
     * @return bool
     */
    public function hasCoupon(PromotionCouponInterface $coupon);

    /**
     * @return bool
     */
    public function hasCoupons();

    /**
     * @param PromotionCouponInterface $coupon
     */
    public function addCoupon(PromotionCouponInterface $coupon);

    /**
     * @param PromotionCouponInterface $coupon
     */
    public function removeCoupon(PromotionCouponInterface $coupon);

    /**
     * @return Collection|PromotionRuleInterface[]
     */
    public function getRules();

    /**
     * @return bool
     */
    public function hasRules();

    /**
     * @param PromotionRuleInterface $rule
     *
     * @return bool
     */
    public function hasRule(PromotionRuleInterface $rule);

    /**
     * @param PromotionRuleInterface $rule
     */
    public function addRule(PromotionRuleInterface $rule);

    /**
     * @param PromotionRuleInterface $rule
     */
    public function removeRule(PromotionRuleInterface $rule);

    /**
     * @return Collection|PromotionActionInterface[]
     */
    public function getActions();

    /**
     * @return bool
     */
    public function hasActions();

    /**
     * @param PromotionActionInterface $action
     *
     * @return bool
     */
    public function hasAction(PromotionActionInterface $action);

    /**
     * @param PromotionActionInterface $action
     */
    public function addAction(PromotionActionInterface $action);

    /**
     * @param PromotionActionInterface $action
     */
    public function removeAction(PromotionActionInterface $action);
}
