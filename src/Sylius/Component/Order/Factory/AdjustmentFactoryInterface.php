<?php

namespace Sylius\Component\Order\Factory;

use Sylius\Component\Order\Model\AdjustmentInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface AdjustmentFactoryInterface extends FactoryInterface
{
    /**
     * @param string $type
     * @param string $label
     * @param string $amount
     * @param bool $neutral
     *
     * @return AdjustmentInterface
     */
    public function createWithData($type, $label, $amount, $neutral);
}
