<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Promotion\Applicator;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;

/**
 * @author Gorka Laucirica <gorka.lauzirika@gmail.com>
 */
interface OrderItemPromotionAdjustmentsApplicatorInterface
{
    /**
     * @param OrderItemInterface $orderItem
     * @param PromotionInterface $promotion
     * @param int $amount
     */
    public function apply(OrderItemInterface $orderItem, PromotionInterface $promotion, $amount);
}
