<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Promotion\Generator;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class PromotionCouponGeneratorInstruction implements PromotionCouponGeneratorInstructionInterface
{
    /**
     * @var int
     */
    private $amount = 5;
    /**
     * @var int
     */
    private $codeLength = 6;

    /**
     * @var \DateTime
     */
    private $expiresAt;

    /**
     * @var int
     */
    private $usageLimit;

    /**
     * {@inheritdoc}
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * {@inheritdoc}
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * {@inheritdoc}
     */
    public function getCodeLength()
    {
        return $this->codeLength;
    }

    /**
     * {@inheritdoc}
     */
    public function setCodeLength($codeLength)
    {
        $this->codeLength = $codeLength;
    }

    /**
     * {@inheritdoc}
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setExpiresAt(\DateTime $expiresAt = null)
    {
        $this->expiresAt = $expiresAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsageLimit()
    {
        return $this->usageLimit;
    }

    /**
     * {@inheritdoc}
     */
    public function setUsageLimit($usageLimit)
    {
        $this->usageLimit = $usageLimit;
    }
}
