<?php
/**
 * @author    Pete Ward <peter.ward@reiss.com>
 * @date      05/02/2016
 * @copyright Copyright (c) Reiss Clothing Ltd.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\OrderBundle\Factory;

use Sylius\Component\Order\Model\Adjustment;
use Sylius\Component\Order\Model\OrderItemUnit;
use Sylius\Component\Resource\Factory\Factory;

/**
 * @author Pete Ward <peter.ward@reiss.com>
 */
class AdjustmentFactory extends Factory
{
    /**
     * @param OrderItemUnit $unit
     * @param string        $label
     *
     * @return Adjustment
     */
    public function createUnitRefund(OrderItemUnit $unit, $label = 'Refund')
    {
        /** @var Adjustment $adjustment */
        $adjustment = $this->createNew();
        $adjustment->setRefund(true);
        $adjustment->setAmount($unit->getOrderItem()->getUnitPrice() * -1);
        $adjustment->setType(Adjustment::UNIT_REFUND_ADJUSTMENT);
        $adjustment->setLabel($label);
        $adjustment->setAdjustable($unit);

        return $adjustment;
    }
}
