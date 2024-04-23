<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Promotion\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\ArchivableTrait;
use Sylius\Component\Resource\Model\TimestampableTrait;
use Sylius\Component\Resource\Model\TranslatableTrait;
use Sylius\Component\Resource\Model\TranslationInterface;

class Promotion implements PromotionInterface
{
    use ArchivableTrait, TimestampableTrait, TranslatableTrait {
        __construct as private initializeTranslationsCollection;
        getTranslation as private doGetTranslation;
    }

    /** @var mixed */
    protected $id;

    /** @var string|null */
    protected $code;

    /** @var string|null */
    protected $name;

    /** @var string|null */
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

    /** @var int|null */
    protected $usageLimit;

    /** @var int */
    protected $used = 0;

    /** @var \DateTimeInterface|null */
    protected $startsAt;

    /** @var \DateTimeInterface|null */
    protected $endsAt;

    /** @var bool */
    protected $couponBased = false;

    /** @var Collection<array-key, PromotionCouponInterface> */
    protected $coupons;

    /** @var Collection<array-key, PromotionRuleInterface> */
    protected $rules;

    /** @var Collection<array-key, PromotionActionInterface> */
    protected $actions;

    protected bool $appliesToDiscounted = true;

    public function __construct()
    {
        $this->initializeTranslationsCollection();

        $this->createdAt = new \DateTime();

        /** @var ArrayCollection<array-key, PromotionCouponInterface> $this->coupons */
        $this->coupons = new ArrayCollection();

        /** @var ArrayCollection<array-key, PromotionRuleInterface> $this->rules */
        $this->rules = new ArrayCollection();

        /** @var ArrayCollection<array-key, PromotionActionInterface> $this->actions */
        $this->actions = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(?int $priority): void
    {
        $this->priority = $priority ?? -1;
    }

    public function isExclusive(): bool
    {
        return $this->exclusive;
    }

    public function setExclusive(?bool $exclusive): void
    {
        $this->exclusive = $exclusive;
    }

    public function getUsageLimit(): ?int
    {
        return $this->usageLimit;
    }

    public function setUsageLimit(?int $usageLimit): void
    {
        $this->usageLimit = $usageLimit;
    }

    public function getUsed(): int
    {
        return $this->used;
    }

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

    public function getStartsAt(): ?\DateTimeInterface
    {
        return $this->startsAt;
    }

    public function setStartsAt(?\DateTimeInterface $startsAt): void
    {
        $this->startsAt = $startsAt;
    }

    public function getEndsAt(): ?\DateTimeInterface
    {
        return $this->endsAt;
    }

    public function setEndsAt(?\DateTimeInterface $endsAt): void
    {
        $this->endsAt = $endsAt;
    }

    public function isCouponBased(): bool
    {
        return $this->couponBased;
    }

    public function setCouponBased(?bool $couponBased): void
    {
        $this->couponBased = (bool) $couponBased;
    }

    public function getCoupons(): Collection
    {
        return $this->coupons;
    }

    public function hasCoupons(): bool
    {
        return !$this->coupons->isEmpty();
    }

    public function hasCoupon(PromotionCouponInterface $coupon): bool
    {
        return $this->coupons->contains($coupon);
    }

    public function addCoupon(PromotionCouponInterface $coupon): void
    {
        if (!$this->hasCoupon($coupon)) {
            $coupon->setPromotion($this);
            $this->coupons->add($coupon);
        }
    }

    public function removeCoupon(PromotionCouponInterface $coupon): void
    {
        $coupon->setPromotion(null);
        $this->coupons->removeElement($coupon);
    }

    public function getRules(): Collection
    {
        return $this->rules;
    }

    public function hasRules(): bool
    {
        return !$this->rules->isEmpty();
    }

    public function hasRule(PromotionRuleInterface $rule): bool
    {
        return $this->rules->contains($rule);
    }

    public function addRule(PromotionRuleInterface $rule): void
    {
        if (!$this->hasRule($rule)) {
            $rule->setPromotion($this);
            $this->rules->add($rule);
        }
    }

    public function removeRule(PromotionRuleInterface $rule): void
    {
        $rule->setPromotion(null);
        $this->rules->removeElement($rule);
    }

    public function getActions(): Collection
    {
        return $this->actions;
    }

    public function hasActions(): bool
    {
        return !$this->actions->isEmpty();
    }

    public function hasAction(PromotionActionInterface $action): bool
    {
        return $this->actions->contains($action);
    }

    public function addAction(PromotionActionInterface $action): void
    {
        if (!$this->hasAction($action)) {
            $action->setPromotion($this);
            $this->actions->add($action);
        }
    }

    public function removeAction(PromotionActionInterface $action): void
    {
        $action->setPromotion(null);
        $this->actions->removeElement($action);
    }

    public function getAppliesToDiscounted(): bool
    {
        return $this->appliesToDiscounted;
    }

    public function setAppliesToDiscounted(bool $applyOnDiscounted): void
    {
        $this->appliesToDiscounted = $applyOnDiscounted;
    }

    public function getLabel(): ?string
    {
        return $this->getTranslation()->getLabel();
    }

    public function setLabel(?string $label): void
    {
        $this->getTranslation()->setLabel($label);
    }

    /** @return PromotionTranslationInterface */
    public function getTranslation(?string $locale = null): TranslationInterface
    {
        /** @var PromotionTranslationInterface $translation */
        $translation = $this->doGetTranslation($locale);

        return $translation;
    }

    protected function createTranslation(): PromotionTranslationInterface
    {
        return new PromotionTranslation();
    }
}
