<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Model;

use Sylius\Component\Order\Model\AdjustmentInterface as BaseAdjustmentInterface;

interface AdjustmentInterface extends BaseAdjustmentInterface
{
    // Labels for tax, shipping and promotion adjustments.
    const ORDER_ITEM_PROMOTION_ADJUSTMENT = 'order_item_promotion';
    const ORDER_PROMOTION_ADJUSTMENT = 'order_promotion';
    const ORDER_SHIPPING_PROMOTION_ADJUSTMENT = 'order_shipping_promotion';
    const ORDER_UNIT_PROMOTION_ADJUSTMENT = 'order_unit_promotion';
    const SHIPPING_ADJUSTMENT = 'shipping';
    const TAX_ADJUSTMENT = 'tax';
}
