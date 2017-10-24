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

namespace Sylius\Component\Promotion\Generator;

interface PromotionCouponGeneratorInstructionInterface
{
    /**
     * @return int|null
     */
    public function getAmount(): ?int;

    /**
     * @param int|null $amount
     */
    public function setAmount(?int $amount): void;

    /**
     * @return int|null
     */
    public function getCodeLength(): ?int;

    /**
     * @param int|null $codeLength
     */
    public function setCodeLength(?int $codeLength): void;

    /**
     * @return \DateTimeInterface|null
     */
    public function getExpiresAt(): ?\DateTimeInterface;

    /**
     * @param \DateTimeInterface $expiresAt
     */
    public function setExpiresAt(?\DateTimeInterface $expiresAt): void;

    /**
     * @return int|null
     */
    public function getUsageLimit(): ?int;

    /**
     * @param int $usageLimit
     */
    public function setUsageLimit(int $usageLimit): void;
}
