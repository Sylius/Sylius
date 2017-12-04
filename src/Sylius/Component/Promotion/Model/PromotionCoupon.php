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

use Sylius\Component\Resource\Model\TimestampableTrait;

class PromotionCoupon implements PromotionCouponInterface
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
     * @var int|null
     */
    protected $usageLimit;

    /**
     * @var int
     */
    protected $used = 0;

    /**
     * @var PromotionInterface
     */
    protected $promotion;

    /**
     * @var \DateTimeInterface
     */
    protected $expiresAt;

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

    /**
     * {@inheritdoc}
     */
    public function getPromotion(): ?PromotionInterface
    {
        return $this->promotion;
    }

    /**
     * {@inheritdoc}
     */
    public function setPromotion(?PromotionInterface $promotion): void
    {
        $this->promotion = $promotion;
    }

    /**
     * {@inheritdoc}
     */
    public function getExpiresAt(): ?\DateTimeInterface
    {
        return $this->expiresAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setExpiresAt(?\DateTimeInterface $expiresAt = null): void
    {
        $this->expiresAt = $expiresAt;
    }

    /**
     * {@inheritdoc}
     */
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
