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
 * Coupon model interface.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface CouponInterface
{
    public function getId();
    public function getCode();
    public function setCode($code);
    public function getUsageLimit();
    public function setUsageLimit($usageLimit);
    public function getUsed();
    public function setUsed($used);
    public function incrementUsed();
    public function isValid();
}
