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

use Sylius\Component\Order\Model\AdjustableInterface;
use Sylius\Component\Order\Model\Adjustment as BaseAdjustment;

/**
 * @author  Pete Ward <peter.ward@reiss.com>
 */
class Adjustment extends BaseAdjustment
{
    /**
     * @var InventoryUnit
     */
    protected $inventoryUnit;

    /**
     * {@inheritdoc}
     */
    public function getAdjustable()
    {
        if (null !== $this->order) {
            return $this->order;
        }

        if (null !== $this->inventoryUnit) {
            return $this->inventoryUnit;
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function setAdjustable(AdjustableInterface $adjustable = null)
    {
        $this->order = $this->inventoryUnit = null;

        if ($adjustable instanceof OrderInterface) {
            $this->order = $adjustable;
        }

        if ($adjustable instanceof InventoryUnitInterface) {
            $this->inventoryUnit = $adjustable;
        }
    }
}