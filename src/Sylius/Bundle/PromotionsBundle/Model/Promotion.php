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
    /**
     * Id
     *
     * @var integer
     */
    protected $id;

    /**
     * Name
     *
     * @var string
     */
    protected $name;

    /**
     * Description
     *
     * @var string
     */
    protected $description;

    /**
     * Usage limit
     *
     * @var integer
     */
    protected $usageLimit;

    /**
     * Number of times this coupon has been used
     *
     * @var integer
     */
    protected $used;

    /**
     * Start date
     *
     * @var \DateTime
     */
    protected $startsAt;

    /**
     * End date
     *
     * @var \DateTime
     */
    protected $endsAt;

    /**
     * Whether this promotion is triggered by a coupon
     *
     * @var Boolean
     */
    protected $couponBased;

    /**
     * Associated coupons
     *
     * @var CouponInterface[]
     */
    protected $coupons;

    /**
     * Associated rules
     *
     * @var RuleInterface[]
     */
    protected $rules;

    /**
     * Associated actions
     *
     * @var ActionInterface[]
     */
    protected $actions;

    /**
     * Last time updated
     *
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * Creation date
     *
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->used = 0;
        $this->couponBased = false;
        $this->coupons = new ArrayCollection();
        $this->rules = new ArrayCollection();
        $this->actions = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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

        return $this;
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

        return $this;
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

        return $this;
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

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function incrementUsed()
    {
        $this->used++;

        return $this;
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

        return $this;
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

        return $this;
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
        $this->couponBased = (Boolean) $couponBased;

        return $this;
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
    public function hasCoupon(CouponInterface $coupon)
    {
        return $this->coupons->contains($coupon);
    }

    /**
     * {@inheritdoc}
     */
    public function addCoupon(CouponInterface $coupon)
    {
        if (!$this->hasCoupon($coupon)) {
            $coupon->setPromotion($this);
            $this->coupons->add($coupon);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeCoupon(CouponInterface $coupon)
    {
        $coupon->setPromotion(null);
        $this->coupons->removeElement($coupon);

        return $this;
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
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * {@inheritdoc}
     */
    public function hasRule(RuleInterface $rule)
    {
        return $this->rules->contains($rule);
    }

    /**
     * {@inheritdoc}
     */
    public function addRule(RuleInterface $rule)
    {
        if (!$this->hasRule($rule)) {
            $rule->setPromotion($this);
            $this->rules->add($rule);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeRule(RuleInterface $rule)
    {
        $rule->setPromotion(null);
        $this->rules->removeElement($rule);

        return $this;
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
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * {@inheritdoc}
     */
    public function hasAction(ActionInterface $action)
    {
        return $this->actions->contains($action);
    }

    /**
     * {@inheritdoc}
     */
    public function addAction(ActionInterface $action)
    {
        if (!$this->hasAction($action)) {
            $action->setPromotion($this);
            $this->actions->add($action);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeAction(ActionInterface $action)
    {
        $action->setPromotion(null);
        $this->actions->removeElement($action);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
