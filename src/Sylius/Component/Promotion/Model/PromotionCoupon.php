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

use Sylius\Component\Resource\Model\TimestampableTrait;

class PromotionCoupon implements PromotionCouponInterface
{
    use TimestampableTrait;

    /** @var mixed */
    protected $id;

    /** @var string|null */
    protected $code;

    /** @var int|null */
    protected $usageLimit;

    /** @var int */
    protected $used = 0;

    /** @var PromotionInterface|null */
    protected $promotion;

    /** @var \DateTimeInterface|null */
    protected $expiresAt;

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

    public function setUsed(int $used): void
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

    public function getPromotion(): ?PromotionInterface
    {
        return $this->promotion;
    }

    public function setPromotion(?PromotionInterface $promotion): void
    {
        $this->promotion = $promotion;
    }

    public function getExpiresAt(): ?\DateTimeInterface
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(?\DateTimeInterface $expiresAt = null): void
    {
        $this->expiresAt = $expiresAt;
    }

    public function isValid(): bool
    {
        if (null !== $this->usageLimit && $this->used >= $this->usageLimit) {
            return false;
        }

        if (null !== $this->expiresAt && $this->expiresAt < new \DateTime()) {
            return false;
        }

        return true;
    }
}
