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
 * Coupon generate instruction.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class Instruction
{
    /**
     * @var int
     */
    protected $amount = 5;

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
     *
     * @return self
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
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
     *
     * @return self
     */
    public function setUsageLimit($usageLimit)
    {
        $this->usageLimit = $usageLimit;

        return $this;
    }
}
