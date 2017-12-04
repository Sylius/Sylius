<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Promotion\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\TimestampableTrait;

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
     * @var \DateTimeInterface
     */
    protected $startsAt;

    /**
     * @var \DateTimeInterface
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
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * {@inheritdoc}
     */
    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * {@inheritdoc}
     */
    public function setPriority(?int $priority): void
    {
        $this->priority = null === $priority ? -1 : $priority;
    }

    /**
     * {@inheritdoc}
     */
    public function isExclusive(): bool
    {
        return $this->exclusive;
    }

    /**
     * {@inheritdoc}
     */
    public function setExclusive(?bool $exclusive): void
    {
        $this->exclusive = $exclusive;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsageLimit(): ?int
    {
        return $this->usageLimit;
    }

    /**
     * {@inheritdoc}
     */
    public function setUsageLimit(?int $usageLimit): void
    {
        $this->usageLimit = $usageLimit;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsed(): int
    {
        return $this->used;
    }

    /**
     * {@inheritdoc}
     */
    public function setUsed(?int $used): void
    {
        $this->used = $used;
    }

    public function incrementUsed(): void
    {
        ++$this->used;
    }

    public function decrementUsed(): void
    {
        --$this->used;
    }

    /**
     * {@inheritdoc}
     */
    public function getStartsAt(): ?\DateTimeInterface
    {
        return $this->startsAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setStartsAt(?\DateTimeInterface $startsAt): void
    {
        $this->startsAt = $startsAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getEndsAt(): ?\DateTimeInterface
    {
        return $this->endsAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setEndsAt(?\DateTimeInterface $endsAt): void
    {
        $this->endsAt = $endsAt;
    }

    /**
     * {@inheritdoc}
     */
    public function isCouponBased(): bool
    {
        return $this->couponBased;
    }

    /**
     * {@inheritdoc}
     */
    public function setCouponBased(?bool $couponBased): void
    {
        $this->couponBased = (bool) $couponBased;
    }

    /**
     * {@inheritdoc}
     */
    public function getCoupons(): Collection
    {
        return $this->coupons;
    }

    /**
     * {@inheritdoc}
     */
    public function hasCoupons(): bool
    {
        return !$this->coupons->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function hasCoupon(PromotionCouponInterface $coupon): bool
    {
        return $this->coupons->contains($coupon);
    }

    /**
     * {@inheritdoc}
     */
    public function addCoupon(PromotionCouponInterface $coupon): void
    {
        if (!$this->hasCoupon($coupon)) {
            $coupon->setPromotion($this);
            $this->coupons->add($coupon);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeCoupon(PromotionCouponInterface $coupon): void
    {
        $coupon->setPromotion(null);
        $this->coupons->removeElement($coupon);
    }

    /**
     * {@inheritdoc}
     */
    public function getRules(): Collection
    {
        return $this->rules;
    }

    /**
     * {@inheritdoc}
     */
    public function hasRules(): bool
    {
        return !$this->rules->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function hasRule(PromotionRuleInterface $rule): bool
    {
        return $this->rules->contains($rule);
    }

    /**
     * {@inheritdoc}
     */
    public function addRule(PromotionRuleInterface $rule): void
    {
        if (!$this->hasRule($rule)) {
            $rule->setPromotion($this);
            $this->rules->add($rule);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeRule(PromotionRuleInterface $rule): void
    {
        $rule->setPromotion(null);
        $this->rules->removeElement($rule);
    }

    /**
     * {@inheritdoc}
     */
    public function getActions(): Collection
    {
        return $this->actions;
    }

    /**
     * {@inheritdoc}
     */
    public function hasActions(): bool
    {
        return !$this->actions->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function hasAction(PromotionActionInterface $action): bool
    {
        return $this->actions->contains($action);
    }

    /**
     * {@inheritdoc}
     */
    public function addAction(PromotionActionInterface $action): void
    {
        if (!$this->hasAction($action)) {
            $action->setPromotion($this);
            $this->actions->add($action);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeAction(PromotionActionInterface $action): void
    {
        $action->setPromotion(null);
        $this->actions->removeElement($action);
    }
}
