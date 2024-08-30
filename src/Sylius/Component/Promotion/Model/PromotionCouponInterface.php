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

use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface PromotionCouponInterface extends CodeAwareInterface, TimestampableInterface, ResourceInterface
{
    public function getUsageLimit(): ?int;

    public function setUsageLimit(?int $usageLimit): void;

    public function getUsed(): int;

    public function setUsed(int $used): void;

    public function incrementUsed(): void;

    public function decrementUsed(): void;

    public function getPromotion(): ?PromotionInterface;

    public function setPromotion(?PromotionInterface $promotion): void;

    public function getExpiresAt(): ?\DateTimeInterface;

    public function setExpiresAt(?\DateTimeInterface $expiresAt): void;

    public function isValid(): bool;
}
