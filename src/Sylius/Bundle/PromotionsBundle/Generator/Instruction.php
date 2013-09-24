<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionsBundle\Generator;

/**
 * Coupon generate instruction.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Instruction
{
    protected $amount;
    protected $usageLimit;

    public function __construct()
    {
        $this->amount = 5;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    public function getUsageLimit()
    {
        return $this->usageLimit;
    }

    public function setUsageLimit($usageLimit)
    {
        $this->usageLimit = $usageLimit;

        return $this;
    }
}
