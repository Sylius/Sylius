<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
    public function createWithData($type, $label, $amount, $neutral = false);
}
