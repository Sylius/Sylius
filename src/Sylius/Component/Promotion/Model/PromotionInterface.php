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
     * @return Collection|CouponInterface[]
     */
    public function getCoupons();

    /**
     * @param CouponInterface $coupon
     *
     * @return bool
     */
    public function hasCoupon(CouponInterface $coupon);

    /**
     * @return bool
     */
    public function hasCoupons();

    /**
     * @param CouponInterface $coupon
     */
    public function addCoupon(CouponInterface $coupon);

    /**
     * @param CouponInterface $coupon
     */
    public function removeCoupon(CouponInterface $coupon);

    /**
     * @return Collection|RuleInterface[]
     */
    public function getRules();

    /**
     * @param RuleInterface $rule
     *
     * @return bool
     */
    public function hasRule(RuleInterface $rule);

    /**
     * @return bool
     */
    public function hasRules();

    /**
     * @param RuleInterface $rule
     */
    public function addRule(RuleInterface $rule);

    /**
     * @param RuleInterface $rule
     */
    public function removeRule(RuleInterface $rule);

    /**
     * @return Collection|ActionInterface[]
     */
    public function getActions();

    /**
     * @param ActionInterface $action
     *
     * @return bool
     */
    public function hasAction(ActionInterface $action);

    /**
     * @return bool
     */
    public function hasActions();

    /**
     * @param ActionInterface $action
     */
    public function addAction(ActionInterface $action);

    /**
     * @param ActionInterface $action
     */
    public function removeAction(ActionInterface $action);
}
