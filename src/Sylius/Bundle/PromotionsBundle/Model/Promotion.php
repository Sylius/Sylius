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

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Promotion model.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class Promotion implements PromotionInterface
{
    protected $id;
    protected $name;
    protected $description;
    protected $usageLimit;
    protected $used;
    protected $startsAt;
    protected $endsAt;
    protected $couponBased;
    protected $coupons;
    protected $rules;
    protected $actions;
    protected $updatedAt;
    protected $createdAt;

    public function __construct()
    {
        $this->used = 0;
        $this->couponBased = false;
        $this->coupons = new ArrayCollection();
        $this->rules = new ArrayCollection();
        $this->actions = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getUsageLimit()
    {
        return $this->usageLimit;
    }

    public function setUsageLimit($usageLimit)
    {
        $this->usageLimit = $usageLimit;
    }

    public function getUsed()
    {
        return $this->used;
    }

    public function setUsed($used)
    {
        $this->used = $used;

        return $this;
    }

    public function incrementUsed()
    {
        $this->used++;
    }

    public function getStartsAt()
    {
        return $this->startsAt;
    }

    public function setStartsAt(\DateTime $startsAt = null)
    {
        $this->startsAt = $startsAt;
    }

    public function getEndsAt()
    {
        return $this->endsAt;
    }

    public function setEndsAt(\DateTime $endsAt = null)
    {
        $this->endsAt = $endsAt;
    }

    public function isCouponBased()
    {
        return $this->couponBased;
    }

    public function setCouponBased($couponBased)
    {
        $this->couponBased = (Boolean) $couponBased;
    }

    public function getCoupons()
    {
        return $this->coupons;
    }

    public function hasCoupons()
    {
        return !$this->coupons->isEmpty();
    }

    public function hasCoupon(CouponInterface $coupon)
    {
        return $this->coupons->contains($coupon);
    }

    public function addCoupon(CouponInterface $coupon)
    {
        if (!$this->hasCoupon($coupon)) {
            $coupon->setPromotion($this);
            $this->coupons->add($coupon);
        }
    }

    public function removeCoupon(CouponInterface $coupon)
    {
        $coupon->setPromotion(null);
        $this->coupons->removeElement($coupon);
    }

    public function hasRules()
    {
        return !$this->rules->isEmpty();
    }

    public function getRules()
    {
        return $this->rules;
    }

    public function hasRule(RuleInterface $rule)
    {
        return $this->rules->contains($rule);
    }

    public function addRule(RuleInterface $rule)
    {
        if (!$this->hasRule($rule)) {
            $rule->setPromotion($this);
            $this->rules->add($rule);
        }
    }

    public function removeRule(RuleInterface $rule)
    {
        $rule->setPromotion(null);
        $this->rules->removeElement($rule);
    }

    public function hasActions()
    {
        return !$this->actions->isEmpty();
    }

    public function getActions()
    {
        return $this->actions;
    }

    public function hasAction(ActionInterface $action)
    {
        return $this->actions->contains($action);
    }

    public function addAction(ActionInterface $action)
    {
        if (!$this->hasAction($action)) {
            $action->setPromotion($this);
            $this->actions->add($action);
        }
    }

    public function removeAction(ActionInterface $action)
    {
        $action->setPromotion(null);
        $this->actions->removeElement($action);
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }
}
