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

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface PromotionInterface extends CodeAwareInterface, TimestampableInterface, ResourceInterface
{
    /**
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void;

    /**
     * @return string|null
     */
    public function getDescription(): ?string;

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void;

    /**
     * @return int
     */
    public function getPriority(): int;

    /**
     * @param int|null $priority
     */
    public function setPriority(?int $priority): void;

    /**
     * @return bool
     */
    public function isExclusive(): bool;

    /**
     * @param bool|null $exclusive
     */
    public function setExclusive(?bool $exclusive): void;

    /**
     * @return int|null
     */
    public function getUsageLimit(): ?int;

    /**
     * @param int|null $usageLimit
     */
    public function setUsageLimit(?int $usageLimit): void;

    /**
     * @return int
     */
    public function getUsed(): int;

    /**
     * @param int $used
     */
    public function setUsed(int $used): void;

    public function incrementUsed(): void;

    public function decrementUsed(): void;

    /**
     * @return \DateTimeInterface|null
     */
    public function getStartsAt(): ?\DateTimeInterface;

    /**
     * @param \DateTimeInterface|null $startsAt
     */
    public function setStartsAt(?\DateTimeInterface $startsAt): void;

    /**
     * @return \DateTimeInterface|null
     */
    public function getEndsAt(): ?\DateTimeInterface;

    /**
     * @param \DateTimeInterface|null $endsAt
     */
    public function setEndsAt(?\DateTimeInterface $endsAt): void;

    /**
     * @return bool
     */
    public function isCouponBased(): bool;

    /**
     * @param bool|null $couponBased
     */
    public function setCouponBased(?bool $couponBased): void;

    /**
     * @return Collection|PromotionCouponInterface[]
     */
    public function getCoupons(): Collection;

    /**
     * @param PromotionCouponInterface $coupon
     *
     * @return bool
     */
    public function hasCoupon(PromotionCouponInterface $coupon): bool;

    /**
     * @return bool
     */
    public function hasCoupons(): bool;

    /**
     * @param PromotionCouponInterface $coupon
     */
    public function addCoupon(PromotionCouponInterface $coupon): void;

    /**
     * @param PromotionCouponInterface $coupon
     */
    public function removeCoupon(PromotionCouponInterface $coupon): void;

    /**
     * @return Collection|PromotionRuleInterface[]
     */
    public function getRules(): Collection;

    /**
     * @return bool
     */
    public function hasRules(): bool;

    /**
     * @param PromotionRuleInterface $rule
     *
     * @return bool
     */
    public function hasRule(PromotionRuleInterface $rule): bool;

    /**
     * @param PromotionRuleInterface $rule
     */
    public function addRule(PromotionRuleInterface $rule): void;

    /**
     * @param PromotionRuleInterface $rule
     */
    public function removeRule(PromotionRuleInterface $rule): void;

    /**
     * @return Collection|PromotionActionInterface[]
     */
    public function getActions(): Collection;

    /**
     * @return bool
     */
    public function hasActions(): bool;

    /**
     * @param PromotionActionInterface $action
     *
     * @return bool
     */
    public function hasAction(PromotionActionInterface $action): bool;

    /**
     * @param PromotionActionInterface $action
     */
    public function addAction(PromotionActionInterface $action): void;

    /**
     * @param PromotionActionInterface $action
     */
    public function removeAction(PromotionActionInterface $action): void;
}
