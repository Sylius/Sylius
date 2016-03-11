<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Factory;

use Sylius\Component\Promotion\Model\ActionInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface ActionFactoryInterface extends FactoryInterface
{
    /**
     * @param int $amount
     *
     * @return ActionInterface
     */
    public function createFixedDiscount($amount);

    /**
     * @param float $percentage
     *
     * @return ActionInterface
     */
    public function createPercentageDiscount($percentage);

    /**
     * @param float $percentage
     *
     * @return ActionInterface
     */
    public function createItemPercentageDiscount($percentage);

    /**
     * @param float $percentage
     *
     * @return ActionInterface
     */
    public function createPercentageShippingDiscount($percentage);
}
