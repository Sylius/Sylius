<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionsBundle\Checker;

use Sylius\Bundle\SalesBundle\Model\OrderInterface;
use Sylius\Bundle\PromotionsBundle\Model\PromotionInterface;

/**
 * Promotion eliglibility checker interface.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface PromotionEliglibilityCheckerInterface
{
    public function isEligible(OrderInterface $order, PromotionInterface $promotion);
}
