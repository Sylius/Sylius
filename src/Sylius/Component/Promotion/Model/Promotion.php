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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\TimestampableTrait;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class Promotion implements PromotionInterface
{
    use TimestampableTrait;

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description;

    /**
     * When exclusive, promotion with top priority will be applied
     *
     * @var int
     */
    protected $priority = 0;

    /**
     * Cannot be applied together with other promotions
     *
     * @var bool
     */
    protected $exclusive = false;

    /**
     * @var int
     */
    protected $usageLimit;

    /**
     * @var int
     */
    protected $used = 0;

    /**
     * @var \DateTime
     */
    protected $startsAt;

    /**
     * @var \DateTime
     */
    protected $endsAt;

    /**
     * @var bool
     */
    protected $couponBased = false;

    /**
     * @var Collection|PromotionCouponInterface[]
     */
    protected $coupons;

    /**
     * @var Collection|PromotionRuleInterface[]
     */
    protected $rules;

    /**
     * @var Collection|PromotionActionInterface[]
     */
    protected $actions;

    public function __construct()
    {
        $this->createdAt = new \DateTime();

        $this->coupons = new ArrayCollection();
        $this->rules = new ArrayCollection();
        $this->actions = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * {@inheritdoc}
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * {@inheritdoc}
     */
    public function setPriority($priority)
    {
        $this->priority = null === $priority ? -1 : $priority;
    }

    /**
     * {@inheritdoc}
     */
    public function isExclusive()
    {
        return $this->exclusive;
    }

    /**
     * {@inheritdoc}
     */
    public function setExclusive($exclusive)
    {
        $this->exclusive = $exclusive;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsageLimit()
    {
        return $this->usageLimit;
    }

    /**
     * {@inheritdoc}
     */
    public function setUsageLimit($usageLimit)
    {
        $this->usageLimit = $usageLimit;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsed()
    {
        return $this->used;
    }

    /**
     * {@inheritdoc}
     */
    public function setUsed($used)
    {
        $this->used = $used;
    }

    public function incrementUsed()
    {
        ++$this->used;
    }

    public function decrementUsed()
    {
        --$this->used;
    }

    /**
     * {@inheritdoc}
     */
    public function getStartsAt()
    {
        return $this->startsAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setStartsAt(\DateTime $startsAt = null)
    {
        $this->startsAt = $startsAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getEndsAt()
    {
        return $this->endsAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setEndsAt(\DateTime $endsAt = null)
    {
        $this->endsAt = $endsAt;
    }

    /**
     * {@inheritdoc}
     */
    public function isCouponBased()
    {
        return $this->couponBased;
    }

    /**
     * {@inheritdoc}
     */
    public function setCouponBased($couponBased)
    {
        $this->couponBased = (bool) $couponBased;
    }

    /**
     * {@inheritdoc}
     */
    public function getCoupons()
    {
        return $this->coupons;
    }

    /**
     * {@inheritdoc}
     */
    public function hasCoupons()
    {
        return !$this->coupons->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function hasCoupon(PromotionCouponInterface $coupon)
    {
        return $this->coupons->contains($coupon);
    }

    /**
     * {@inheritdoc}
     */
    public function addCoupon(PromotionCouponInterface $coupon)
    {
        if (!$this->hasCoupon($coupon)) {
            $coupon->setPromotion($this);
            $this->coupons->add($coupon);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeCoupon(PromotionCouponInterface $coupon)
    {
        $coupon->setPromotion(null);
        $this->coupons->removeElement($coupon);
    }

    /**
     * {@inheritdoc}
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * {@inheritdoc}
     */
    public function hasRules()
    {
        return !$this->rules->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function hasRule(PromotionRuleInterface $rule)
    {
        return $this->rules->contains($rule);
    }

    /**
     * {@inheritdoc}
     */
    public function addRule(PromotionRuleInterface $rule)
    {
        if (!$this->hasRule($rule)) {
            $rule->setPromotion($this);
            $this->rules->add($rule);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeRule(PromotionRuleInterface $rule)
    {
        $rule->setPromotion(null);
        $this->rules->removeElement($rule);
    }

    /**
     * {@inheritdoc}
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * {@inheritdoc}
     */
    public function hasActions()
    {
        return !$this->actions->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function hasAction(PromotionActionInterface $action)
    {
        return $this->actions->contains($action);
    }

    /**
     * {@inheritdoc}
     */
    public function addAction(PromotionActionInterface $action)
    {
        if (!$this->hasAction($action)) {
            $action->setPromotion($this);
            $this->actions->add($action);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeAction(PromotionActionInterface $action)
    {
        $action->setPromotion(null);
        $this->actions->removeElement($action);
    }
}
