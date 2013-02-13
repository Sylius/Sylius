<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionsBundle\Model;

/**
 * Coupon model.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class Coupon implements CouponInterface
{
    protected $id;
    protected $code;
    protected $usageLimit;
    protected $used;
    protected $promotion;

    public function __construct()
    {
        $this->used = 0;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function setCode($code)
    {
        $this->code = $code;
    }

    public function getUsageLimit()
    {
        return $this->usageLimit;
    }

    public function setUsageLimit($usageLimit)
    {
        $this->usageLimit = $usageLimit;
    }

    public function getUsed()
    {
        return $this->used;
    }

    public function incrementUsed()
    {
        $this->used++;
    }

    public function getPromotion()
    {
        return $this->promotion;
    }

    public function setPromotion(PromotionInterface $promotion = null)
    {
        $this->promotion = $promotion;
    }
}
