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

namespace Sylius\Component\Promotion\Generator;

interface PromotionCouponGeneratorInstructionInterface
{
    public function getAmount(): ?int;

    public function setAmount(?int $amount): void;

    public function getPrefix(): ?string;

    public function setPrefix(?string $prefix): void;

    public function getCodeLength(): ?int;

    public function setCodeLength(?int $codeLength): void;

    public function getSuffix(): ?string;

    public function setSuffix(?string $suffix): void;

    public function getExpiresAt(): ?\DateTimeInterface;

    /**
     * @param \DateTimeInterface $expiresAt
     */
    public function setExpiresAt(?\DateTimeInterface $expiresAt): void;

    public function getUsageLimit(): ?int;

    public function setUsageLimit(int $usageLimit): void;
}
