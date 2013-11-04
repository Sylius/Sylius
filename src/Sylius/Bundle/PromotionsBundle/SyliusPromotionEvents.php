<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionsBundle;

/**
 * Promotions are used to give discounts or other types of rewards to customers.
 *
 * @author Julien Janvier <j.janvier@gmail.com>
 */
class SyliusPromotionEvents
{
    const COUPON_INVALID         = 'sylius.promotion.coupon_invalid';
    const COUPON_ELIGIBLE        = 'sylius.promotion.coupon_eligible';
    const COUPON_NOT_ELIGIBLE    = 'sylius.promotion.coupon_not_eligible';
}
