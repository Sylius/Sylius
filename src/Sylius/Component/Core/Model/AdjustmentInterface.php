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

interface AdjustmentInterface extends BaseAdjustmentInterface, OriginAwareInterface
{
    // Labels for tax, shipping and promotion adjustments.
    const TAX_ADJUSTMENT       = 'tax';
    const SHIPPING_ADJUSTMENT  = 'shipping';
    const PROMOTION_ADJUSTMENT = 'promotion';
}
