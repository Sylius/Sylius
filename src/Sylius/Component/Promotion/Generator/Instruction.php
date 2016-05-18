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
class Instruction implements InstructionInterface
{
    /**
     * @var int
     */
    protected $amount = 5;
    /**
     * @var int
     */
    protected $codeLength = 6;

    /**
     * @var \DateTime
     */
    protected $expiresAt;

    /**
     * @var int
     */
    protected $usageLimit;

    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param int $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return int
     */
    public function getCodeLength()
    {
        return $this->codeLength;
    }

    /**
     * @param int $codeLength
     */
    public function setCodeLength($codeLength)
    {
        $this->codeLength = $codeLength;
    }

    /**
     * @return \DateTime
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * @param \DateTime $expiresAt
     */
    public function setExpiresAt(\DateTime $expiresAt = null)
    {
        $this->expiresAt = $expiresAt;
    }

    /**
     * @return int
     */
    public function getUsageLimit()
    {
        return $this->usageLimit;
    }

    /**
     * @param int $usageLimit
     */
    public function setUsageLimit($usageLimit)
    {
        $this->usageLimit = $usageLimit;
    }
}
