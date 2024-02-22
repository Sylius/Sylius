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

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\ArchivableInterface;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;

interface PromotionInterface extends ArchivableInterface, CodeAwareInterface, TimestampableInterface, TranslatableInterface, ResourceInterface
{
    public function getName(): ?string;

    public function setName(?string $name): void;

    public function getDescription(): ?string;

    public function setDescription(?string $description): void;

    public function getPriority(): int;

    public function setPriority(?int $priority): void;

    public function isExclusive(): bool;

    public function setExclusive(?bool $exclusive): void;

    public function getUsageLimit(): ?int;

    public function setUsageLimit(?int $usageLimit): void;

    public function getUsed(): int;

    public function setUsed(int $used): void;

    public function incrementUsed(): void;

    public function decrementUsed(): void;

    public function getStartsAt(): ?\DateTimeInterface;

    public function setStartsAt(?\DateTimeInterface $startsAt): void;

    public function getEndsAt(): ?\DateTimeInterface;

    public function setEndsAt(?\DateTimeInterface $endsAt): void;

    public function isCouponBased(): bool;

    public function setCouponBased(?bool $couponBased): void;

    /**
     * @return Collection<array-key, PromotionCouponInterface>
     */
    public function getCoupons(): Collection;

    public function hasCoupon(PromotionCouponInterface $coupon): bool;

    public function hasCoupons(): bool;

    public function addCoupon(PromotionCouponInterface $coupon): void;

    public function removeCoupon(PromotionCouponInterface $coupon): void;

    /**
     * @return Collection<array-key, PromotionRuleInterface>
     */
    public function getRules(): Collection;

    public function hasRules(): bool;

    public function hasRule(PromotionRuleInterface $rule): bool;

    public function addRule(PromotionRuleInterface $rule): void;

    public function removeRule(PromotionRuleInterface $rule): void;

    /**
     * @return Collection<array-key, PromotionActionInterface>
     */
    public function getActions(): Collection;

    public function hasActions(): bool;

    public function hasAction(PromotionActionInterface $action): bool;

    public function addAction(PromotionActionInterface $action): void;

    public function removeAction(PromotionActionInterface $action): void;

    public function getAppliesToDiscounted(): bool;

    public function setAppliesToDiscounted(bool $applyOnDiscounted): void;

    public function getLabel(): ?string;

    public function setLabel(?string $label): void;
}
