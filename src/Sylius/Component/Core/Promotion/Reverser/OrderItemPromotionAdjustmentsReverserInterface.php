<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Promotion\Reverser;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;

/**
 * @author Gorka Laucirica <gorka.lauzirika@gmail.com>
 */
interface OrderItemPromotionAdjustmentsReverserInterface
{
    /**
     * @param OrderInterface $order
     * @param PromotionInterface $promotion
     */
    public function revert(OrderInterface $order, PromotionInterface $promotion);
}
