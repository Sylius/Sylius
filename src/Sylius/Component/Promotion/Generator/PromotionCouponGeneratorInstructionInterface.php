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

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface PromotionCouponGeneratorInstructionInterface
{
    /**
     * @return int
     */
    public function getAmount();

    /**
     * @param int $amount
     */
    public function setAmount($amount);

    /**
     * @return int
     */
    public function getCodeLength();

    /**
     * @param int $codeLength
     */
    public function setCodeLength($codeLength);

    /**
     * @return \DateTimeInterface
     */
    public function getExpiresAt();

    /**
     * @param \DateTimeInterface $expiresAt
     */
    public function setExpiresAt(\DateTimeInterface $expiresAt = null);

    /**
     * @return int
     */
    public function getUsageLimit();

    /**
     * @param int $usageLimit
     */
    public function setUsageLimit($usageLimit);
}
