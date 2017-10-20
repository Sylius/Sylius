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

use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface PromotionCouponInterface extends CodeAwareInterface, TimestampableInterface, ResourceInterface
{
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
     * @return PromotionInterface
     */
    public function getPromotion(): ?PromotionInterface;

    /**
     * @param PromotionInterface|null $promotion
     */
    public function setPromotion(?PromotionInterface $promotion): void;

    /**
     * @return \DateTimeInterface|null
     */
    public function getExpiresAt(): ?\DateTimeInterface;

    /**
     * @param \DateTimeInterface $expiresAt
     */
    public function setExpiresAt(?\DateTimeInterface $expiresAt): void;

    /**
     * @return bool
     */
    public function isValid(): bool;
}
